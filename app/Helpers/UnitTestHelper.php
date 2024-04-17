<?php

declare(strict_types=1);

namespace App\Helpers;

use Faker\Factory;

class UnitTestHelper
{
    public static array $countryCodes = [
        'KE',
        'NG',
    ];

    /**
     * Generate string without special characters
     */
    public static function fakerStringWithoutSpecialCharacters(): ?string
    {
        return preg_replace('/[^A-Za-z0-9 ]/', '', Factory::create()->company);
    }

    /**
     * Make mobile number
     */
    public static function makeMobileNumber(bool $international = true): string
    {
        if ($international) {
            return Factory::create()->regexify('2547[0-9]{8}');
        } else {
            return Factory::create()->regexify('07[0-9]{8}');
        }
    }

    /**
     * Make random date
     */
    public static function makeRandomDate(): string
    {
        return DateHelper::now()->addDays(rand(0, 10))->toDateTimeString();
    }

    /**
     * Get random country name
     */
    public static function getRandomCountryName(): string
    {
        return Factory::create()->country();
    }

    /**
     * Get random region name
     */
    public static function getRandomRegionName(): string
    {
        return Factory::create()->company();
    }

    /**
     * Get random city name
     */
    public static function getRandomCityName(): string
    {
        return Factory::create()->city;
    }

    /**
     * Get
     */
    public static function getRandomURL(): string
    {
        return Factory::create()->url();
    }

    /**
     * Get popular course category codes
     */
    public static function getPopularCourseCategoryCodes(): array
    {
        return [
            '1004173101',
            '8225352828',
            '8419092584',
            '7159183926',
        ];
    }

    /**
     * Sample base64 image
     * @return string
     **/
    public static function getSampleBase64Image(): string
    {
        return file_get_contents(storage_path('app/dummy-base64-image.txt'));
    }

    /**
     * @return array
     */
    public static function getLinkedCoursesCodes():array
    {
        return [
            '1004173101',
            '8225352828',
            '8419092584',
            '7159183926',
        ];
    }

    /**
     * Valid KCSE eligibility options
    */
    public static function validKCSEEligibilityOtherSubjectsOptions(): array
    {
        return [
            "Biology:C OR Chemistry:C OR Physics:C OR Geography:C",
            "3C's",
            "Biology:C+ OR Chemistry:C+ OR Physics:C+",
            "Physics:C"
        ];
    }

    /**
     * Valid ALevel eligibility options
    */
    public static function validALevelEligibilityOtherSubjectsOptions(): array
    {
        return [
            "B",
            "Physics AS & A:B",
            "English Language AS & A:C OR English Literature AS & A:C",
            "(Additional Mathematics US:C OR Cambridge International Mathematics:C OR Mathematics:C OR Mathematics US:C) AND (English:C OR English US:C) AND 3C's"
        ];
    }

    /**
     * Valid ASLevel eligibility options
    */
    public static function validASLevelEligibilityOtherSubjectsOptions(): array
    {
        return [
            "English Language AS & A:C OR English Literature AS & A:C",
            "C",
            "Biology AS & A:C OR Physics AS & A:C OR Chemistry AS & A:C OR Geography AS & A:C OR Mathematics AS & A:C OR Mathematics Further A:C"
        ];
    }

    /**
     * Valid IGCSE eligibility options
    */
    public static function validIGCSEEligibilityOtherSubjectsOptions(): array
    {
        return [
            "3C's",
            "C",
        ];
    }

    /**
     * Valid IGCSE eligibility options
    */
    public static function validIBEligibilityOtherSubjectsOptions(): array
    {
        return [
            "Literature HL:4 OR Literature SL:4 OR Language & Literature HL:4 OR Language & Literature SL:4 OR Literature & Performance:4",
            "Physics HL:5",
        ];
    }

    /**
     * Valid WAEC eligibility options
    */
    public static function validWAECEligibilityOtherSubjectsOptions(): array
    {
        return [
            "Chemistry:C4 AND Physics:C4 AND 3C4's",
            "3C6's",
            "Chemistry:B3 OR Physics:B3"
        ];
    }

    /**
     * Create Duolingo english test data
    */
    public static function createDuolingoEnglishTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(10, 160),
            'literacy_score' => Factory::create()->numberBetween(10, 160),
            'comprehension_score' => Factory::create()->numberBetween(10, 160),
            'conversation_score' => Factory::create()->numberBetween(10, 160),
            'production_score' => Factory::create()->numberBetween(10, 160)
        ];
    }

    /**
     * Create TOEFL english test data
    */
    public static function createTOEFLEnglishTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(0, 120),
            'reading_score' => Factory::create()->numberBetween(0, 30),
            'listening_score' => Factory::create()->numberBetween(0, 30),
            'speaking_score' => Factory::create()->numberBetween(0, 30),
            'writing_score' => Factory::create()->numberBetween(0, 30)
        ];
    }

    /**
     * Create IELTS english test data
    */
    public static function createIELTSEnglishTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(0, 9),
            'reading_score' => Factory::create()->numberBetween(0, 9),
            'listening_score' => Factory::create()->numberBetween(0, 9),
            'speaking_score' => Factory::create()->numberBetween(0, 9),
            'writing_score' => Factory::create()->numberBetween(0, 9)
        ];
    }

    /**
     * Create IELTS english test data
    */
    public static function createPTEEnglishTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(10, 90),
            'reading_score' => Factory::create()->numberBetween(10, 90),
            'listening_score' => Factory::create()->numberBetween(10, 90),
            'speaking_score' => Factory::create()->numberBetween(10, 90),
            'writing_score' => Factory::create()->numberBetween(10, 90)
        ];
    }

    /**
     * Create GMAT test data
    */
    public static function createGMATTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(200, 800),
            'verbal_score' => Factory::create()->numberBetween(6, 51),
            'quantitative_score' => Factory::create()->numberBetween(6, 51),
            'awa_score' => Factory::create()->numberBetween(0, 6),
        ];
    }

    /**
     * Create GRE test data
    */
    public static function createGRETestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(260, 340),
            'verbal_score' => Factory::create()->numberBetween(130, 170),
            'quantitative_score' => Factory::create()->numberBetween(130, 170),
            'awa_score' => Factory::create()->numberBetween(0, 6),
        ];
    }

    /**
     * Create UCAT test data
    */
    public static function createUCATTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(260, 340),
            'verbal_score' => Factory::create()->numberBetween(300, 900),
            'decision_making_score' => Factory::create()->numberBetween(300, 900),
            'quantitative_reasoning_score' => Factory::create()->numberBetween(300, 900),
            'abstract_reasoning_score' => Factory::create()->numberBetween(300, 900),
            'situational_judgement_score' => Factory::create()->numberBetween(1200, 3600),
        ];
    }

    /**
     * Create SAT test data
    */
    public static function createSATTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(400, 1600),
            'reading_score' => Factory::create()->numberBetween(1, 15),
            'writing_language_score' => Factory::create()->numberBetween(10, 40),
            'mathematics_score' => Factory::create()->numberBetween(10, 40)
        ];
    }

    /**
     * Create ACT test data
    */
    public static function createACTTestData(): array
    {
        return [
            'overall_score' => Factory::create()->numberBetween(1, 36),
            'english_score' => Factory::create()->numberBetween(1, 36),
            'mathematics_score' => Factory::create()->numberBetween(1, 36),
            'reading_score' => Factory::create()->numberBetween(1, 36),
            'science_score' => Factory::create()->numberBetween(1, 36),
        ];
    }

    /**
     * Get possible campus codes
    */
    public static function getPossibleCampusCodes(): array
    {
        return [
            1503161857,
            4964828000,
            4412969951,
            2716231891,
            5964780569,
        ];
    }

    /**
     * Get possible campus names
    */
    public static function getPossibleCampusNames(): array
    {
        return [
            'Hello Crawfords',
            'Main Campus',
            'Sub Campus',
        ];
    }

    /**
     * Get possible campus names
    */
    public static function makePossibleCampusList(): array
    {
        $data = [[
            'name' => Factory::create()->randomElement(self::getPossibleCampusNames()),
            'code' => Factory::create()->randomElement(self::getPossibleCampusCodes()),
            'image' => Factory::create()->imageUrl,
            'state_name' => Factory::create()->city,
            'country_name' => Factory::create()->country,
            'country_iso2_code' => 'USD',
            'city_name' => Factory::create()->city
        ],[
            'name' => Factory::create()->randomElement(self::getPossibleCampusNames()),
            'code' => Factory::create()->randomElement(self::getPossibleCampusCodes()),
            'image' => Factory::create()->imageUrl,
            'state_name' => Factory::create()->city,
            'country_name' => Factory::create()->country,
            'country_iso2_code' => 'KES',
            'city_name' => Factory::create()->city
        ],[
            'name' => Factory::create()->randomElement(self::getPossibleCampusNames()),
            'code' => Factory::create()->randomElement(self::getPossibleCampusCodes()),
            'image' => Factory::create()->imageUrl,
            'state_name' => Factory::create()->city,
            'country_name' => Factory::create()->country,
            'country_iso2_code' => 'KES',
            'city_name' => Factory::create()->city
        ]];

        return collect($data)->unique('code')->toArray();
    }
}
