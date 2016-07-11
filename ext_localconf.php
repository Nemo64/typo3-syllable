<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['syllable'] = [
    'backend' => TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
    'frontend' => TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][] = 'Syllable\Hooks\Render->renderPostProcess';