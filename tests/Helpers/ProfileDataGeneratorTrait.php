<?php


namespace Tests\Helpers;

use App\Packages\Profiles\Enums\LanguageFluency;
use App\Packages\Profiles\Models\ProfileFactory;
use App\Packages\Profiles\Repository\Arango\Repository;
use App\Packages\Profiles\ProfileServiceInterface;
use App\Packages\Profiles\Models\Metadata;
use App\Packages\Profiles\Models\ProfileManifest;
use App\Packages\Profiles\Models\CV;
use App\Packages\Profiles\Models\Document;
use App\Packages\Profiles\Repository\RepositoryInterface;

trait ProfileDataGeneratorTrait
{
    /**
     * Create new profile and insert it in database.
     */
    private function insertTestProfile(): ProfileManifest
    {
        $profile = ProfileFactory::createProfileManifestFromArray([
            'ownerArn' => 'arn:local:api:100',
            'metadata' => [
                'title' => 'test metadata title',
            ]
        ]);

        return app()
            ->make(ProfileServiceInterface::class)
            ->createProfile($profile);
    }

    /**
     * Insert  test profiles.
     */
    private function insertTestProfiles(int $count): array
    {
        $returnProfiles = [];
      for ($i = 0; $i < $count; $i++) {
          $returnProfiles[] = $this->insertTestProfile();
      }

      return $returnProfiles;
    }

    /**
     * Create new cvs for a profile defined by $profileId and insert it in database.
     */
    private function insertTestProfileCvs(string $profileId ,CV|array ...$cvs): array
    {
        $profileService = app()->make(ProfileServiceInterface::class);
        $outputCvs = [];
        foreach($cvs as $cv) {
            if (is_array($cv)) {
                $cv = CV::fromArray($cv);
            }

            $outputCvs [] = $profileService->addProfileCv($profileId, $cv);
        }
        return $outputCvs;
    }

    /**
     * Create new cv and insert it in database.
     */
    private function insertTestCv(ProfileManifest $profile, array $cv = []): CV
    {
        $profileRepository = app()->make(RepositoryInterface::class);
        $data = empty($cv) ? ['lang' => 'ar'] : $cv;
        $cv   = ProfileFactory::createCvFromArray($data);
        $cv->setId(
            $profileRepository->addProfileCv($profile->getId(), $cv)
        );

        return $cv;
    }

    /**
     * Insert testing profile Document in database.
     */
    public function insertTestDocument(Repository $arangoRepository, string $profileId): Document
    {
        $doc = new Document();
        $doc->setType("cover_letter");
        $doc->setId($arangoRepository->addProfileDocument($profileId, $doc));

        return $doc;
    }

    /**
     * Create new cv with positions and insert it in database.
     */
    private function insertTestCvWithPositions(ProfileManifest $profile): CV
    {
        $cv = [
            'lang' => 'ar',
            'positions' => [
                ['id' => '1234', 'title' => 'title 1'],
                ['id' => '5678', 'title' => 'title 2']
            ]
        ];

        return $this->insertTestCv($profile, $cv);
    }

    /**
     * Create new cv with skills and insert it in database.
     */
    private function insertTestCvWithSkills(ProfileManifest $profile): CV
    {
        $cv = [
            'lang' => 'ar',
            'skills' => [
                ['id' => '1234', 'label'=> 'dev'],
                ['id' => '5678', 'label'=> 'web']
            ]
        ];

        return $this->insertTestCv($profile, $cv);
    }

    /**
     * Create new cv with languages and insert it in database.
     */
    private function insertTestCvWithLanguages(ProfileManifest $profile): CV
    {
        $cv = [
            'lang' => 'ar',
            'languages' => [
                ['id' => '123', 'code' => 'ar', 'fluency' => LanguageFluency::NATIVE],
                ['id' => '456', 'code' => 'en', 'fluency' => LanguageFluency::BASIC]
            ]
        ];

        return $this->insertTestCv($profile, $cv);
    }

    /**
     * Create new cv with educations and insert it in database.
     */
    private function insertTestCvWithEducations(ProfileManifest $profile): CV
    {
        $cv = [
            'lang' => 'ar',
            'education' => [
                ['id' => '1234', 'title' => 'title 1'],
                ['id' => '5678', 'title' => 'title 2'],
            ]
        ];

        return $this->insertTestCv($profile, $cv);
    }

    /**
     * Create new cv and insert it in database.
     */
    private function insertTestDoc(string $profileId): Document
    {
        $document             = new Document();
        $document->setUrl('https://www.domain.com/path_to_file/filename.pdf');
        $profileService = app()->make(ProfileServiceInterface::class);

        return $profileService->addProfileDocument($profileId, $document);
    }
}
