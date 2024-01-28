<?php

namespace BitCode\FI\Triggers\Spectra;

use BitCode\FI\Triggers\FormHook\FormHookController;

final class SpectraController extends FormHookController
{
    public static function info()
    {
        return [
            'name' => 'Spectra',
            'title' => 'Get callback data through an URL',
            'type' => 'form_hook',
            'is_active' => true,
        ];
    }
}
