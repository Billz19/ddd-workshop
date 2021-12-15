<?php

namespace App\Packages\Posts\Models;

use App\Library\JsonSchemaValidator\ModelJsonSchemaValidatorTrait;
use App\Library\Serialize\ArraySerializableInterface;
use App\Library\Serialize\ArraySerializableTrait;

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
    private string $creator;

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
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }


}
