<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Controller\FrontendModule;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\TwoFactor\Authenticator;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use ParagonIE\ConstantTime\Base32;
use Scheb\TwoFactorBundle\Security\Authentication\Exception\InvalidTwoFactorCodeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

class TwoFactorController extends AbstractFrontendModuleController
{
    /**
     * @var PageModel
     */
    protected $page;

    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null, PageModel $page = null): Response
    {
        $this->page = $page;

        if (
            $this->page instanceof PageModel
            && $this->get('contao.routing.scope_matcher')->isFrontendRequest($request)
        ) {
            $this->page->loadDetails();
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();

        $services['contao.framework'] = ContaoFramework::class;
        $services['contao.routing.scope_matcher'] = ScopeMatcher::class;
        $services['contao.security.two_factor.authenticator'] = Authenticator::class;
        $services['security.authentication_utils'] = AuthenticationUtils::class;
        $services['security.token_storage'] = TokenStorageInterface::class;
        $services['translator'] = TranslatorInterface::class;

        return $services;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $token = $this->get('security.token_storage')->getToken();

        if (!$token instanceof TokenInterface) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $user = $token->getUser();

        if (!$user instanceof FrontendUser) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        /** @var PageModel $adapter */
        $adapter = $this->get('contao.framework')->getAdapter(PageModel::class);

        $redirectPage = $model->jumpTo > 0 ? $adapter->findByPk($model->jumpTo) : null;
        $return = $redirectPage instanceof PageModel ? $redirectPage->getAbsoluteUrl() : $this->page->getAbsoluteUrl();

        $template->action = '';
        $template->enforceTwoFactor = $this->page->enforceTwoFactor;
        $template->targetPath = $return;

        $translator = $this->get('translator');

        // Inform the user if 2FA is enforced
        if ($this->page->enforceTwoFactor) {
            $template->message = $translator->trans('MSC.twoFactorEnforced', [], 'contao_default');
        }

        if ((!$user->useTwoFactor && $this->page->enforceTwoFactor) || 'enable' === $request->get('2fa')) {
            $response = $this->enableTwoFactor($template, $request, $user, $return);

            if (null !== $response) {
                return $response;
            }
        }

        if (!$this->page->enforceTwoFactor && 'tl_two_factor_disable' === $request->request->get('FORM_SUBMIT')) {
            $response = $this->disableTwoFactor($user);

            if (null !== $response) {
                return $response;
            }
        }

        $template->isEnabled = (bool) $user->useTwoFactor;
        $template->href = $this->page->getAbsoluteUrl().'?2fa=enable';
        $template->twoFactor = $translator->trans('MSC.twoFactorAuthentication', [], 'contao_default');
        $template->explain = $translator->trans('MSC.twoFactorExplain', [], 'contao_default');
        $template->active = $translator->trans('MSC.twoFactorActive', [], 'contao_default');
        $template->enableButton = $translator->trans('MSC.enable', [], 'contao_default');
        $template->disableButton = $translator->trans('MSC.disable', [], 'contao_default');

        return new Response($template->parse());
    }

    private function enableTwoFactor(Template $template, Request $request, FrontendUser $user, string $return): ?Response
    {
        // Return if 2FA is enabled already
        if ($user->useTwoFactor) {
            return null;
        }

        $translator = $this->get('translator');
        $authenticator = $this->get('contao.security.two_factor.authenticator');
        $exception = $this->get('security.authentication_utils')->getLastAuthenticationError();

        if ($exception instanceof InvalidTwoFactorCodeException) {
            $template->message = $translator->trans('ERR.invalidTwoFactor', [], 'contao_default');
        }

        // Validate the verification code
        if ('tl_two_factor' === $request->request->get('FORM_SUBMIT')) {
            if ($authenticator->validateCode($user, $request->request->get('verify'))) {
                // Enable 2FA
                $user->useTwoFactor = '1';
                $user->save();

                return new RedirectResponse($return);
            }

            $template->message = $translator->trans('ERR.invalidTwoFactor', [], 'contao_default');
        }

        // Generate the secret
        if (!$user->secret) {
            $user->secret = random_bytes(128);
            $user->save();
        }

        $template->enable = true;
        $template->secret = Base32::encodeUpperUnpadded($user->secret);
        $template->textCode = $translator->trans('MSC.twoFactorTextCode', [], 'contao_default');
        $template->qrCode = base64_encode($authenticator->getQrCode($user, $request));
        $template->scan = $translator->trans('MSC.twoFactorScan', [], 'contao_default');
        $template->verify = $translator->trans('MSC.twoFactorVerification', [], 'contao_default');
        $template->verifyHelp = $translator->trans('MSC.twoFactorVerificationHelp', [], 'contao_default');

        return null;
    }

    private function disableTwoFactor(FrontendUser $user): ?Response
    {
        // Return if 2FA is disabled already
        if (!$user->useTwoFactor) {
            return null;
        }

        $user->secret = null;
        $user->useTwoFactor = '';
        $user->save();

        return new RedirectResponse($this->page->getAbsoluteUrl());
    }
}
