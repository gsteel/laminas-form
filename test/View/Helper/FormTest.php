<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\View\Helper\Form as FormHelper;
use LaminasTest\Form\TestAsset\CityFieldset;

class FormTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormHelper();
        parent::setUp();
    }

    public function testInvokeReturnsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCallingOpenTagWithoutProvidingFormResultsInEmptyActionAndGetMethod()
    {
        $markup = $this->helper->openTag();
        $this->assertContains('<form', $markup);
        $this->assertContains('action=""', $markup);
        $this->assertContains('method="get"', $markup);
    }

    public function testCallingCloseTagEmitsClosingFormTag()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</form>', $markup);
    }

    public function testCallingOpenTagWithFormUsesFormAttributes()
    {
        $form = new Form();
        $attributes = array(
            'method'  => 'post',
            'action'  => 'http://localhost/endpoint',
            'class'   => 'login',
            'id'      => 'form-login',
            'enctype' => 'application/x-www-form-urlencoded',
            'target'  => '_self',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);

        $escape = $this->renderer->plugin('escapehtml');
        foreach ($attributes as $attribute => $value) {
            $this->assertContains(sprintf('%s="%s"', $attribute, $escape($value)), $markup);
        }
    }

    public function testOpenTagUsesNameAsIdIfNoIdAttributePresent()
    {
        $form = new Form();
        $attributes = array(
            'name'  => 'login-form',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);
        $this->assertContains('name="login-form"', $markup);
        $this->assertContains('id="login-form"', $markup);
    }

    public function testRender()
    {
        $form = new Form();
        $attributes = array('name'  => 'login-form');
        $form->setAttributes($attributes);
        $form->add(new CityFieldset());
        $form->add(new Submit('send'));

        $markup = $this->helper->__invoke($form);

        $this->assertContains('<form', $markup);
        $this->assertContains('id="login-form"', $markup);
        $this->assertContains('<label><span>Name of the city</span>', $markup);
        $this->assertContains('<fieldset><legend>Country</legend>', $markup);
        $this->assertContains('<input type="submit" name="send"', $markup);
        $this->assertContains('</form>', $markup);
    }

    public function testRenderPreparesForm()
    {
        $form = $this->getMock('Laminas\\Form\\Form');
        $form->expects($this->once())->method('prepare');
        $form->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $form->expects($this->any())->method('getIterator')->will($this->returnValue(new \ArrayIterator(array())));

        $markup = $this->helper->__invoke($form);

        $this->assertContains('<form', $markup);
        $this->assertContains('</form>', $markup);
    }
}
