<?php

namespace CustomConcepts\Tableratezipranges\Plugin\Transsmart\Rate\Response;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Rate\Response\Parser;
use CustomConcepts\Tableratezipranges\Api\TablerateGeneratorInterface;
use CustomConcepts\Tableratezipranges\Helper\Config;

class ParserPlugin
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var TablerateGeneratorInterface
     */
    private $rateGenerator;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @param Config $helper
     * @param TablerateGeneratorInterface $rateGenerator
     * @param ProfileRepository $profileRepository
     */
    public function __construct(
        Config $helper,
        TablerateGeneratorInterface $rateGenerator,
        ProfileRepository $profileRepository
    ) {
        $this->helper = $helper;
        $this->rateGenerator = $rateGenerator;
        $this->profileRepository = $profileRepository;
    }

    public function afterGetFixedRates(Parser $subject, array $result, $activeProfiles)
    {
        if ($this->helper->isTableRateZipRangesEnabled()) {
            $result = [];
            $rates = $this->rateGenerator->getRates();
            foreach ($rates as $rate) {
                $profile = $this->profileRepository->load($rate['transsmart_bookingprofile_id']);
                if (array_key_exists($profile->getCode(), $activeProfiles)) {
                    $profileRate = $activeProfiles[$profile->getCode()];
                    $profileRate->salesPrice = $rate['price'];
                    $result[$profileRate->carrier][] = $profileRate;
                }
            }
        }

        return $result;
    }
}
