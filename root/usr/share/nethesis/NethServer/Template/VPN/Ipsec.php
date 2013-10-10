<?php

/* @var $view Nethgui\Renderer\Xhtml */

echo $view->fieldsetSwitch('status', 'enabled', Nethgui\Renderer\WidgetFactoryInterface::FIELDSETSWITCH_CHECKBOX)->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->fieldset()->setAttribute('template', $T('Authentication_label'))
        ->insert($view->radioButton('KeyType', 'rsa'))
        ->insert($view->fieldsetSwitch('KeyType', 'psk', \Nethgui\Renderer\WidgetFactoryInterface::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->textInput('KeyPskSecret', \Nethgui\Renderer\WidgetFactoryInterface::LABEL_NONE)->setAttribute('class', 'labeled-control'))))
    ->insert($view->fieldset()->setAttribute('template', $T('L2tpSubnet_label'))
        ->insert($view->textInput('L2tpNetwork'))
        ->insert($view->textInput('L2tpNetmask')))
;

echo $view->buttonList($view::BUTTON_SUBMIT);
