<?php
/**
 * Bigace - a PHP and MySQL based Web CMS.
 *
 * LICENSE
 *
 * This source file is subject to the new GNU General Public License
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.bigace.de/license.html
 *
 * Bigace is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Form.php 130 2011-03-16 17:57:44Z kevin $
 */

/**
 * The formular to fetch data for a new comment.
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Comment_Form extends Bigace_Zend_Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function init()
    {
        loadLanguageFile('comments');

        // ----------- action element -----------
        $element = $this->createElement(
            'hidden', 'doComment', array(
                'value' => 'create',
                'required' => false
            )
        );
        $element->setDecorators(array('ViewHelper'));
        $this->addElement($element);

        // ----------- name -----------
        $this->addElement(
            'text', 'name', array(
                'label' => getTranslation('comment.formular.name'),
                'filters' => array('StringTrim', 'StripTags'),
                'required' => true
            )
        );

        $emailRequired = Bigace_Config::get("comments", "email.required", false);

        // ----------- email -----------
        $element = $this->createElement(
            'text', 'email', array(
                'label' => (
                    $emailRequired ?
                    getTranslation('comment.formular.email.required') :
                    getTranslation('comment.formular.email')
                ),
                'filters' => array('StringTrim', 'StripTags'),
                'required' => $emailRequired
            )
        );

        if ($emailRequired) {
            $element->addValidator(new Zend_Validate_EmailAddress());
        }

        $this->addElement($element);

        // ----------- homepage -----------
        $useHomepage = Bigace_Config::get("comments", "ask.for.homepage", true);

        if ($useHomepage) {
            $element = $this->createElement(
                'text', 'homepage', array(
                    'label'    => getTranslation('comment.formular.homepage'),
                    'filters'  => array('StringTrim', 'StripTags'),
                    'required' => false
                )
            );
            // TODO skip the check because we have no translations
            //$element->addValidator(new Bigace_Zend_Validate_Uri());
            $this->addElement($element);
        }

        // ----------- comment -----------
        $element = $this->createElement(
            'textarea', 'comment', array(
                'label'    => getTranslation('comment.formular.comment'),
                'filters'  => array('StringTrim'),
                'required' => true
            )
        );

        if (Bigace_Config::get("comments", "allow.html", false) === false) {
            $element->addFilter('StripTags');
        }

        $this->addElement($element);

        $useCaptcha = Bigace_Config::get("comments", "use.captcha", false);
        if ($useCaptcha) {
            $element = $this->createCaptcha('captcha', getTranslation('comment.formular.captcha'));
            $this->addElement($element);
        }

        // ----------- submit button -----------
        $this->addElement(
            'button', 'createButton', array(
                'label'    => getTranslation('comment.formular.create'),
                'type'     => 'submit',
                'required' => false,
                'class'    => 'button'
            )
        );

    }
}
