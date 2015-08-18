<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;


/**
 * Reads and writes FAQ categories
 *
 * @property integer $id
 * @property integer $tstamp
 * @property string  $title
 * @property string  $headline
 * @property integer $jumpTo
 * @property boolean $allowComments
 * @property string  $notify
 * @property string  $sortOrder
 * @property integer $perPage
 * @property boolean $moderate
 * @property boolean $bbcode
 * @property boolean $requireLogin
 * @property boolean $disableCaptcha
 *
 * @method static \FaqCategoryModel findById($id, $opt=array())
 * @method static \FaqCategoryModel findByPk($id, $opt=array())
 * @method static \FaqCategoryModel findByIdOrAlias($val, $opt=array())
 * @method static \FaqCategoryModel findOneBy($col, $val, $opt=array())
 * @method static \FaqCategoryModel findOneByTstamp($val, $opt=array())
 * @method static \FaqCategoryModel findOneByTitle($val, $opt=array())
 * @method static \FaqCategoryModel findOneByHeadline($val, $opt=array())
 * @method static \FaqCategoryModel findOneByJumpTo($val, $opt=array())
 * @method static \FaqCategoryModel findOneByAllowComments($val, $opt=array())
 * @method static \FaqCategoryModel findOneByNotify($val, $opt=array())
 * @method static \FaqCategoryModel findOneBySortOrder($val, $opt=array())
 * @method static \FaqCategoryModel findOneByPerPage($val, $opt=array())
 * @method static \FaqCategoryModel findOneByModerate($val, $opt=array())
 * @method static \FaqCategoryModel findOneByBbcode($val, $opt=array())
 * @method static \FaqCategoryModel findOneByRequireLogin($val, $opt=array())
 * @method static \FaqCategoryModel findOneByDisableCaptcha($val, $opt=array())
 *
 * @method static \Model\Collection|\FaqCategoryModel findByTstamp($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByTitle($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByHeadline($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByJumpTo($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByAllowComments($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByNotify($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findBySortOrder($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByPerPage($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByModerate($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByBbcode($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByRequireLogin($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findByDisableCaptcha($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\FaqCategoryModel findAll($opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByTitle($val, $opt=array())
 * @method static integer countByHeadline($val, $opt=array())
 * @method static integer countByJumpTo($val, $opt=array())
 * @method static integer countByAllowComments($val, $opt=array())
 * @method static integer countByNotify($val, $opt=array())
 * @method static integer countBySortOrder($val, $opt=array())
 * @method static integer countByPerPage($val, $opt=array())
 * @method static integer countByModerate($val, $opt=array())
 * @method static integer countByBbcode($val, $opt=array())
 * @method static integer countByRequireLogin($val, $opt=array())
 * @method static integer countByDisableCaptcha($val, $opt=array())
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class FaqCategoryModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_faq_category';

}
