<?php

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\CampaignObjectiveValues;
use FacebookAds\Object\Values\InsightsOperators;

require 'vendor/autoload.php';
require 'FacebookSettings.php';

class FacebookCampaignFiltering
{

    const CAMPAIGN_FIELDS = [
        CampaignFields::ID,
        CampaignFields::NAME,
        CampaignFields::OBJECTIVE,
        CampaignFields::CREATED_TIME,
    ];

    /**
     * This methods retrieves facebook campaigns that are at least 10 days old.
     */
    public static function filterByCreationDate()
    {
        $facebookSettings = new FacebookSettings();

        // The facebook api has to be initiated before use
        Api::init($facebookSettings->getAppId(), $facebookSettings->getAppSecret(), $facebookSettings->getUserToken());

        $adAccount = new AdAccount($facebookSettings->getDefaultAdAccount());

        // Unix time stamp
        $since = Carbon::now('America/Los_Angeles')
            ->subDays(10)
            ->timestamp;

        $campaigns = $adAccount->getCampaigns(self::CAMPAIGN_FIELDS, [
            'filtering' => [
                [
                    'field' => 'campaign.created_time',
                    'operator' => InsightsOperators::GREATER_THAN,
                    'value' => $since,
                ]
            ]
        ]);

        foreach ($campaigns as $campaign) {
            var_dump($campaign->{CampaignFields::NAME});
            var_dump($campaign->{CampaignFields::ID});
            var_dump($campaign->{CampaignFields::OBJECTIVE});
            var_dump($campaign->{CampaignFields::CREATED_TIME});
        }

    }

    /**
     * This method retrieves facebook campaigns that have a link click objective.
     */
    public static function filterByObjective()
    {
        $facebookSettings = new FacebookSettings();

        // The facebook api has to be initiated before use
        Api::init($facebookSettings->getAppId(), $facebookSettings->getAppSecret(), $facebookSettings->getUserToken());

        $adAccount = new AdAccount($facebookSettings->getDefaultAdAccount());

        // The In operator expect an array of possible values
        $campaigns = $adAccount->getCampaigns(self::CAMPAIGN_FIELDS, [
            'filtering' => [
                [
                    'field' => 'campaign.' . CampaignFields::OBJECTIVE,
                    'operator' => InsightsOperators::IN,
                    'value' => [
                        CampaignObjectiveValues::LINK_CLICKS
                    ]
                ]
            ]
        ]);

        foreach ($campaigns as $campaign) {
            var_dump($campaign->{CampaignFields::NAME});
            var_dump($campaign->{CampaignFields::ID});
            var_dump($campaign->{CampaignFields::OBJECTIVE});
            var_dump($campaign->{CampaignFields::CREATED_TIME});
        }
    }

    /**
     * This methods retrieves facebook campaigns whose spend cap is
     * between $100 and $150.
     */
    public static function filterBySpendCap()
    {
        $facebookSettings = new FacebookSettings();

        // The facebook api has to be initiated before use
        Api::init($facebookSettings->getAppId(), $facebookSettings->getAppSecret(), $facebookSettings->getUserToken());

        $adAccount = new AdAccount($facebookSettings->getDefaultAdAccount());

        // $100
        $minValue = 100 * 100;

        //$150
        $maxValue = 150 * 100;

        // The In Range operator expects an array with two values that create a range.
        $campaigns = $adAccount->getCampaigns(self::CAMPAIGN_FIELDS, [
            'filtering' => [
                [
                    'field' => 'campaign.' . CampaignFields::SPEND_CAP,
                    'operator' => InsightsOperators::IN_RANGE,
                    'value' => [
                        $minValue,
                        $maxValue
                    ]
                ]
            ]
        ]);

        foreach ($campaigns as $campaign) {
            var_dump($campaign->{CampaignFields::NAME});
            var_dump($campaign->{CampaignFields::ID});
            var_dump($campaign->{CampaignFields::OBJECTIVE});
            var_dump($campaign->{CampaignFields::CREATED_TIME});
        }
    }
}

FacebookCampaignFiltering::filterByCreationDate();
FacebookCampaignFiltering::filterByObjective();
FacebookCampaignFiltering::filterBySpendCap();