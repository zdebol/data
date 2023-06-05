<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Bundle\DataGridBundle\Fixtures;

use FSi\Component\Files\WebFile;

class Entity
{
    private int $id;
    private string $name;
    private ?string $author = null;
    private ?WebFile $file = null;
    private ?WebFile $image = null;
    private ?EntityCategory $category = null;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getFile(): ?WebFile
    {
        return $this->file;
    }

    public function setFile(?WebFile $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getImage(): ?WebFile
    {
        return $this->image;
    }

    public function setImage(?WebFile $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCategory(): ?EntityCategory
    {
        return $this->category;
    }

    public function setCategory(?EntityCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
