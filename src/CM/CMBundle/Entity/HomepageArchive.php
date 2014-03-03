<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HomepageArchive
 *
 * @ORM\Entity(repositoryClass="HomepageArchiveRepository")
 * @ORM\Table(name="homepage_archive")
 */
class HomepageArchive
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="article_id", type="integer")
     */
    private $articleId;

    /**
     * @ORM\OneToOne(targetEntity="Article", inversedBy="homepageArchive")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $article;

    /**
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="HomepageCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $category;

    public static function className()
    {
        return get_class();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get articleId
     *
     * @return integer 
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * Set article
     *
     * @param Article $article
     * @return Like
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
        if (!is_null($article)) {
            $this->articleId = $article->getId();
        }
    
        return $this;
    }

    /**
     * Get article
     *
     * @return Article 
     */
    public function getArticle()
    {
        return $this->article;
    }+

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setCategory(HomepageCategory $category)
    {
        $this->category = $category;
        if (!is_null($category)) {
            $this->categoryId = $category->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
