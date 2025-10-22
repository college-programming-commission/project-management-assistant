<?php

namespace Alison\ProjectManagementAssistant\Enums;

enum NavigationGroup: string
{
    case Administration = 'Адміністрування';
    case ProjectManagement = 'Управління проектами';
    case EventManagement = 'Управління подіями';
    case StudyManagement = 'Управління навчанням';
}
