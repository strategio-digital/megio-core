<?php
declare(strict_types=1);

namespace App\Article\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Entity]
#[ORM\Table(name: '`article`')]
#[ORM\HasLifecycleCallbacks]
class Article implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    ## DONE
    #[ORM\Column]
    protected string $title;
    
    ## DONE
    #[ORM\Column]
    protected string $slug;
    
    ## DONE
    #[ORM\Column(type: 'text')]
    protected string $content;
    
    ## DONE
    #[ORM\ManyToOne(targetEntity: ArticleAuthor::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    protected ?ArticleAuthor $author = null;
    
    ## TODO:
    /** @var Collection<int, ArticleTag> */
    #[ORM\ManyToMany(targetEntity: ArticleTag::class, inversedBy: 'articles')]
    #[ORM\JoinTable(name: 'article_has_article_tag')]
    protected Collection $tags;
    
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
    
    /**
     * @return array{fields: string[], format: string}
     */
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['title'],
            'format' => '%s'
        ];
    }
}