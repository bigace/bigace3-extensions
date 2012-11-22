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
 * @package    Bigace_Form
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Form.php 130 2011-03-16 17:57:44Z kevin $
 */

/**
 * The formular to fetch data for the contact form, all infos for a simple email message.
 *
 * @category   Bigace
 * @package    Bigace_Form
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Form_ContactMail extends Bigace_Zend_Form
{
    /**
     * @var array
     */
    private $optional = array();
    /**
     * @var boolean
     */
    private $captcha  = false;

    public function __construct($options = null, $captcha = false, $optionalEments = array())
    {
        $this->optional = $optionalEments;
        $this->captcha  = $captcha;

        parent::__construct($options);
    }

    public function init()
    {
        $this->addTranslation('contactMail');
        $this->setAttrib('class', 'contactMail');

        $element = $this->createElement(
            'text', 'name', array(
                'label'    => 'form_name',
                'filters'  => array('StringTrim', 'StripTags'),
                'required' => false
            )
        );
        $this->addElement($element);

        $element = $this->createElement(
            'text', 'from', array(
                'label'      => 'form_email',
                'filters'    => array('StringTrim', 'StripTags'),
                'validators' => array('EmailAddress'),
                'required'   => true
            )
        );
        $this->addElement($element);

        $element = $this->createElement(
            'text', 'subject', array(
                'label'    => 'form_subject',
                'filters'  => array('StringTrim', 'StripTags'),
                'required' => false
            )
        );
        $this->addElement($element);

        foreach ($this->optional AS $key => $title) {
            $element = $this->createElement(
                'text', $key, array(
                    'label'    => $title,
                    'filters'  => array('StringTrim', 'StripTags'),
                    'required' => false
                )
            );
            $this->addElement($element);
        }

        $element = $this->createElement(
            'textarea', 'message', array(
                'label'    => 'form_message',
                'filters'  => array('StringTrim', 'StripTags'),
                'required' => true
            )
        );
        $this->addElement($element);

        if ($this->captcha) {
            $element = $this->createCaptcha('captcha', 'form_captcha');
            $this->addElement($element);
        }

        $this->addElement(
            'button', 'createButton', array(
                'label'    => 'form_send',
                'type'     => 'submit',
                'required' => false,
                'class'    => 'button'
            )
        );

    }
}