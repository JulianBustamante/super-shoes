<?php

namespace SuperShoesBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use SuperShoesBundle\Form\ArticleType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Rest controller for articles
 *
 * @package SuperShoesBundle\Controller
 * @author Julián Bustamante <julian.bustamante@pixelula.com>
 */
class ArticleController extends FOSRestController
{
    /**
     * List all articles.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing articles.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many articles to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getArticlesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset + 1;
        $limit = $paramFetcher->get('limit');

        $articles = $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')->findBy(array(), null, $limit, $offset);

        return new View($articles);
    }

    /**
     * Get single Article.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an article for a given id",
     *   output = "SuperShoesBundle\Entity\Article",
     *   requirements = {
     *     {
     *       "name" = "id",
     *       "dataType" = "integer",
     *       "requirement" = "\d+",
     *       "description" = "The article id"
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Bad request",
     *     401 = "Returned when the user is not authorized",
     *     404 = "Returned when the article is not found",
     *     500 = "Server error"
     *   }
     * )
     *
     * @Annotations\View(templateVar="article")
     *
     * @param Request $request the request object
     * @param int     $id      the article id
     *
     * @return array
     *
     * @throws NotFoundHttpException when article not exist
     */
    public function getArticleAction(Request $request, $id)
    {
        $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')
            ->find($id);

        $data = array();
        $statusCode = 200;
        $success = true;

        if (null === $article) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $data['error_code'] = $statusCode;
            $data['error_message'] = Response::$statusTexts[$statusCode];
            $success = false;
        }
        else {
            $data['article'] = $article;
        }

        $data += array(
            'success' => $success,
        );

        $view = $this->view($data, $statusCode);

        return $this->handleView($view);
    }

    /**
     * Presents the form to use to create a new article.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return FormTypeInterface
     */
    public function newArticleAction()
    {
        return $this->createForm(ArticleType::class);
    }

    /**
     * Presents the form to use to update an existing article.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the article is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param int     $id      the article id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when article not exist
     */
    public function editArticleAction(Request $request, $id)
    {
        $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')
            ->find($id);

        if (null === $article) {
            throw $this->createNotFoundException("Article does not exist.");
        }

        $form = $this->createForm(ArticleType::class, $article);
        return $form;
    }
}
