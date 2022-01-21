<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Bundle\DataSourceBundle\Fixtures;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class News
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $author = null;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private ?string $shortContent = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?DateTimeImmutable $createDate = null;

    /**
     * @ORM\Column(type="time_immutable")
     */
    private ?DateTimeImmutable $createTime = null;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="news")
     */
    private ?Category $category = null;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     */
    private ?Category $category2 = null;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     *
     * @var Collection<int,Group>
     */
    private Collection $groups;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $tags = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = false;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setShortContent(?string $shortContent): void
    {
        $this->shortContent = $shortContent;
    }

    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setCreateDate(?DateTimeImmutable $createDate): void
    {
        $this->createDate = $createDate;
    }

    public function getCreateDate(): ?DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateTime(?DateTimeImmutable $createTime): void
    {
        $this->createTime = $createTime;
    }

    public function getCreateTime(): ?DateTimeImmutable
    {
        return $this->createTime;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory2(Category $category2): void
    {
        $this->category2 = $category2;
    }

    public function getCategory2(): ?Category
    {
        return $this->category2;
    }

    /**
     * @return Collection<int,Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function setTags(string $tags): void
    {
        $this->tags = $tags;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setActive(bool $active = true): void
    {
        $this->active = $active;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
