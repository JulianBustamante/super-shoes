<?php

namespace SuperShoesBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use SuperShoesBundle\Entity\Article;
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
     *     200 = "Returned when successful",
     *     400 = "Bad request",
     *     401 = "Returned when the user is not authorized",
     *     404 = "Returned when the article is not found",
     *     500 = "Server error"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing articles.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many articles to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return View
     */
    public function getArticlesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset + 1;
        $limit = $paramFetcher->get('limit');

        // Get all articles between required limits.
        $articles = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')->findBy(array(), null, $limit, $offset);

        // Construct the response.
        $data = $this->get('super_shoes.utils.webservice')->processResponse($articles, 'articles');

        return $this->view($data, $data['status_code']);
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
     * @Annotations\View(
     *     templateVar = "article"
     * )
     *
     * @param Request $request the request object
     * @param int $id the article id
     *
     * @return View
     *
     * @throws NotFoundHttpException when article not exist
     */
    public function getArticleAction(Request $request, $id)
    {
        // Find the article.
        $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')
            ->find($id);

        // Construct the response.
        $data = $this->get('super_shoes.utils.webservice')->processResponse($article, 'article');
        $view = $this->view($data, $data['status_code']);
        $view->setTemplate("SuperShoesBundle:Article:getArticle.html.twig");

        return $view;
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
     * @return \Symfony\Component\Form\Form
     */
    public function newArticleAction()
    {
        return $this->createForm(ArticleType::class, null, array(
            'action' => $this->generateUrl('supershoes_post_articles')));
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
     * @param int $id the article id
     *
     * @return \Symfony\Component\Form\Form
     *
     * @throws NotFoundHttpException when article not exist
     */
    public function editArticleAction(Request $request, $id)
    {
        $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Article')
            ->find($id);

        if (null === $article) {
            throw $this->createNotFoundException('Article does not exist.');
        }

        $form = $this->createForm(ArticleType::class, $article, array(
            'action' => $this->generateUrl('supershoes_put_articles',
            array('id' => $article->getId())))
        );
        return $form;
    }

    /**
     * Creates a new article from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "SuperShoesBundle\Form\ArticleType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postArticlesAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Persist the new article.
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $view = $this->routeRedirectView('supershoes_get_article', array('id' => $article->getId()));
            return $view;
        }
        else {
            // Show the form with the validation errors.
            return $this->view($form)->setTemplate("SuperShoesBundle:Article:editArticle.html.twig");
        }
    }


    /**
     * Update existing article from the submitted data or create a new note at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "SuperShoesBundle\Form\ArticleType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the article id
     *
     * @return \Symfony\Component\Form\Form | View
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function putArticlesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('SuperShoesBundle:Article')->find($id);

        $form = $this->createForm(ArticleType::class, $article, array('method' => 'PUT'));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            $view = $this->routeRedirectView('supershoes_get_article', array('id' => $article->getId()));
            $view->setTemplate("SuperShoesBundle:Article:getArticle.html.twig");
            return $view;
        }
        return $this->view($form);
    }
}
