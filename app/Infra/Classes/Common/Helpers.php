<?php


namespace App\Infra\Classes\Common;


use App\Infra\Interfaces\Repositories\Common\GalleryRepositoryInterface;
use App\Infra\Interfaces\Resources\ResourceInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class Helpers
{
    public static function emptyJsonObject()
    {
        return (object)null;
    }

    public static function extractIdsFromLeafToParent($repositoryData): array
    {
        $ids = [];
        if ($repositoryData) {
            while ($repositoryData->parent_id != null) {
                $ids[] = $repositoryData->id;
                $repositoryData = $repositoryData->parent;
            }
            $ids[] = $repositoryData->id;
        }
        return $ids;
    }

    public static function datesInputToArray(int $pickType, string $datesString, string $fromFormat = Constants::DEFAULT_WEBSITE_DATE_FORMAT, $toFormat = Constants::DEFAULT_DATE_FORMAT): array
    {
        $separatedDates = [];
        if ($pickType == Constants::DATE_TYPE_SINGLE) {
            $separatedDates[] = self::dateToFormat($datesString, $fromFormat, $toFormat);
        } elseif ($pickType == Constants::DATE_TYPE_MULTIPLE) {
            $separatedDates = self::multipleDatesToFormat(explode(', ', $datesString), $fromFormat, $toFormat);
        } elseif ($pickType == Constants::DATE_TYPE_RANGE) {
            $separatedDates = explode(' - ', $datesString);
            $separatedDates = self::datesRangeToFormat($separatedDates[0], $separatedDates[1], $fromFormat, $toFormat);
        }
        return $separatedDates;
    }

    public static function dateToFormat(string $dateString, string $fromFormat, $toFormat = Constants::DEFAULT_DATE_FORMAT): string
    {
        $carbonDate = Carbon::createFromFormat($fromFormat, $dateString);
        return $carbonDate->format($toFormat);
    }

    public static function multipleDatesToFormat(array $dates, string $fromFormat = Constants::DEFAULT_WEBSITE_DATE_FORMAT, $toFormat = Constants::DEFAULT_DATE_FORMAT): array
    {
        $formattedDates = [];
        foreach ($dates as $date) {
            $formattedDates[] = self::dateToFormat($date, $fromFormat, $toFormat);
        }
        return $formattedDates;
    }

    public static function datesRangeToFormat(string $start, string $end, string $fromFormat = Constants::DEFAULT_WEBSITE_DATE_FORMAT, $toFormat = Constants::DEFAULT_DATE_FORMAT): array
    {
        $formattedDates = [];
        $start = self::dateToFormat($start, $fromFormat, Constants::DEFAULT_DATE_FORMAT);
        $end = self::dateToFormat($end, $fromFormat, Constants::DEFAULT_DATE_FORMAT);
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $formattedDates[] = $date->format($toFormat);
        }
        return $formattedDates;
    }

    public static function mapBannerActionToValues(string $action, ResourceInterface $resource)
    {
        $jsonArray = null;
        $keys = Constants::getBannerActionParams($action);
        foreach ((array)$keys as $key) {
            $jsonArray[$key] = $resource->get($key);
        }
        return $jsonArray;
    }

    public static function mapBannerActionToJsonManipulator(string $action, ResourceInterface $resource): ?array
    {
        switch ($action) {
            case Constants::BANNER_ACTION_NO_ACTION:
                return DBJsonManipulator::actionNoAction($resource);
            case Constants::BANNER_ACTION_CALL:
                return DBJsonManipulator::actionCall($resource);
            case Constants::BANNER_ACTION_INTERNAL:
                return DBJsonManipulator::actionInternal($resource);
            case Constants::BANNER_ACTION_EXTERNAL:
                return DBJsonManipulator::actionExternal($resource);
        }
        return null;
    }

    public static function uploadImage(UploadedFile $file, string $uploadPath = null)
    {
        $filename = self::generateRandomFileName($file->getClientOriginalExtension());
        if (\App::environment(['local'])) {
            if ($file->storeAs($uploadPath, $filename)) {
                return $filename;
            }
        } elseif ($file->storeAs($uploadPath, $filename, 's3')) {
            return $filename;
        }
        return false;
    }

    public static function generateRandomFileName(string $fileExtension = null)
    {
        return rand(0, 20000) . time() . '.' . $fileExtension;
    }

    public static function addToGallery(GalleryRepositoryInterface $galleryRepository, string $uploadPath = null, $inputName = null): int
    {
        $inputName = $inputName ?: 'path';
        if (\request()->hasFile($inputName) && \request()->file($inputName)->isValid()) {
            $fileName = Helpers::uploadImage(\request()->file($inputName), $uploadPath ?: $galleryRepository->getUploadsPath());
            return $galleryRepository->create([
                'path' => $fileName
            ])->id;
        }
        return 0;
    }

    public static function acceptedImageExtensions()
    {
        return ['jpg', 'jpeg', 'png'];
    }


    public static function acceptedVideoExtensions()
    {
        return ['mp4'];
    }


    private static function countClassifiedAdsPerPage()
    {
        return (int)(Constants::PER_PAGE / (Constants::CLASSIFIED_RESULT_PER_ROW + Constants::COMMERCIAL_RESULT_PER_ROW)) * Constants::CLASSIFIED_RESULT_PER_ROW;
    }

    private static function countCommercialAdsPerPage()
    {
        return (int)(Constants::PER_PAGE / (Constants::CLASSIFIED_RESULT_PER_ROW + Constants::COMMERCIAL_RESULT_PER_ROW)) * Constants::COMMERCIAL_RESULT_PER_ROW;
    }

    public static function countAdsOfTypePerPage(bool $isClassified): int
    {
        return $isClassified ? self::countClassifiedAdsPerPage() : self::countCommercialAdsPerPage();
    }

    public static function wordsFromCustomTagsString(string $keywords): array
    {
        $keywords = explode(' ', $keywords);
        foreach ($keywords as $index => $keyword) {
            if (mb_strlen($keyword) < 2) {
                unset($keywords[$index]);
            }
        }
        return $keywords;
    }

    public static function setDefaultToDateTimeColumn(string $table, string $column)
    {
        DB::statement('alter table '
            . $table
            . ' alter column '
            . $column
            . ' set default CURRENT_TIMESTAMP'
        );
    }
}
