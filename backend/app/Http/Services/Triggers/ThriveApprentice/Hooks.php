<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\ThriveApprentice\ThriveApprenticeController;

Hooks::addAction('thrive_apprentice_course_finish', [ThriveApprenticeController::class, 'handleCourseComplete'], 10, 2);
Hooks::addAction('thrive_apprentice_lesson_complete', [ThriveApprenticeController::class, 'handleLessonComplete'], 10, 2);
Hooks::addAction('thrive_apprentice_module_finish', [ThriveApprenticeController::class, 'handleModuleComplete'], 10, 2);
