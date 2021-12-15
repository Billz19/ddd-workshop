<?php

namespace App\Packages\Posts\Models;

use App\Library\JsonSchemaValidator\ModelJsonSchemaValidatorTrait;
use App\Library\Serialize\ArraySerializableInterface;
use App\Library\Serialize\ArraySerializableTrait;
use Carbon\Carbon;

class Post implements  ArraySerializableInterface, \JsonSerializable
{
    use ArraySerializableTrait;
    use ModelJsonSchemaValidatorTrait;

    public const POST_SCHEMA_PATH = __DIR__ . '/../Schemas/CreatePost.json';
    public const PUT_SCHEMA_PATH = __DIR__ . '/../Schemas/UpdatePost.json';

    private string $id;
    private string $title;
    private string $content;
    private string $imageUrl;
    private string $createdAt;
    private string $updatedAt;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt = null): void
    {
        $createdAt ??= Carbon::now()->toRfc3339String();
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt = null): void
    {
        $updatedAt ??= Carbon::now()->toRfc3339String();
        $this->updatedAt = $updatedAt;
    }


}
