<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 09.07.16
 * Time: 17:14
 */

namespace Syllable\Hooks;


use Syllable\Cache\SyllableCacheAdapter;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class Render
{
    // TODO this should be in configuration
    private $minLettersBefore = 4;
    private $minLettersAfter = 4;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    public function renderPostProcess(&$parts)
    {
        preg_match('#lang="([\w-]+)"#i', $parts['htmlTag'], $htmlTagParts);
        if (empty($htmlTagParts)) {
            return;
        }

        $lang = $htmlTagParts[1];
        if (empty($lang)) {
            return;
        }

        switch ($lang) {
            case "en":
                $lang = "en-us";
                break;
            case "de":
                $lang = "de-1996";
                break;
            case "de-ch":
                $lang = "de-ch-1901";
                break;
            case "en-gb":
            case "en-us":
                break;
            // TODO there are more exceptions here which i currently ignore
            default:
                // remove every variation on that language
                $lang = preg_replace('/-.*/s', '', $lang);
        }

        $syllable = new \Syllable($lang);

        /** @var CacheManager $cacheManager */
        $cacheManager = $this->objectManager->get(CacheManager::class);
        $syllable->setCache(new SyllableCacheAdapter($cacheManager->getCache('syllable')));

        $htmlExpression = '#(<(?:[pbisa]|h\d|div|li|strong)[^>]*>)([^<]+)(?=<)#is';
        $parts['bodyContent'] = preg_replace_callback($htmlExpression, function ($groups) use ($syllable) {
            return $groups[1] . preg_replace_callback('/[\'[:alpha:]]+/', function ($word) use ($syllable) {
                $wordParts = $syllable->splitWord($word[0]);

                // join syllables at the beginning if the first syllable is to short
                while (count($wordParts) > 1 && strlen(reset($wordParts)) < $this->minLettersBefore) {
                    $part1 = array_shift($wordParts);
                    $part2 = array_shift($wordParts);
                    array_unshift($wordParts, $part1 . $part2);
                }

                // join syllables at the end if the last syllable is to short
                while (count($wordParts) > 1 && strlen(end($wordParts)) < $this->minLettersAfter) {
                    $part1 = array_pop($wordParts);
                    $part2 = array_pop($wordParts);
                    array_push($wordParts, $part2 . $part1);
                }

                return implode("&shy;", $wordParts);
            }, $groups[2]);
        }, $parts['bodyContent']);
    }
}