<?php

namespace SuperShoesBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use SuperShoesBundle\Entity\Store;
use SuperShoesBundle\Form\StoreType;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Rest controller for stores
 *
 * @package SuperShoesBundle\Controller
 * @author Julián Bustamante <julian.bustamante@pixelula.com>
 */
class StoreController extends FOSRestController
{
    /**
     * List all stores.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing stores.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many stores to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return ArticleCollection
     */
    public function getStoresAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset + 1;
        $limit = $paramFetcher->get('limit');

        $stores = $store = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Store')->findBy(array(), null, $limit, $offset);

        return new ArticleCollection($stores, $offset, $limit);
    }

    /**
     * Get single Store.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an store for a given id",
     *   output = "SuperShoesBundle\Entity\Store",
     *   requirements = {
     *     {
     *       "name" = "id",
     *       "dataType" = "integer",
     *       "requirement" = "\d+",
     *       "description" = "The store id"
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Bad request",
     *     401 = "Returned when the user is not authorized",
     *     404 = "Returned when the store is not found",
     *     500 = "Server error"
     *   }
     * )
     *
     * @Annotations\View(templateVar="store")
     *
     * @param Request $request the request object
     * @param int     $id      the store id
     *
     * @return View
     *
     * @throws NotFoundHttpException when store not exist
     */
    public function getStoreAction(Request $request, $id)
    {
        $store = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Store')
            ->find($id);

        if (null === $store) {
            throw $this->createNotFoundException("Store does not exist.");
        }

        return $this->view($store);
    }

    /**
     * Presents the form to use to create a new store.
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
    public function newStoreAction(): Form {
        return $this->createForm(StoreType::class);
    }

    /**
     * Presents the form to use to update an existing store.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the store is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param int     $id      the store id
     *
     * @return Form
     *
     * @throws NotFoundHttpException when store not exist
     */
    public function editStoreAction(Request $request, $id): Form {
        $store = $store = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Store')
            ->find($id);
        if (null === $store) {
            throw $this->createNotFoundException('Store does not exist.');
        }

        return $this->createForm(StoreType::class, $store);
    }

    /**
     * Creates a new store from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "SuperShoesBundle\Form\StoreType",
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
    public function postStoresAction(Request $request)
    {
        $store = new Store();
        $form = $this->createForm(StoreType::class, $store);
        $form->submit($request);
        if ($form->isValid()) {
            return $this->routeRedirectView('supershoes_get_store', array('id' => $store->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * Load all the articles from a specific store.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\Get("/articles/stores/{id}")
     *
     * @param Request $request  the request object
     * @param int     $id the store id.
     *
     * @return View
     */
    public function getStoreArticlesAction(Request $request, $id)
    {
        $articles = $article = $this->getDoctrine()
            ->getRepository('SuperShoesBundle:Store')->findAllArticles($id);

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
            $data = array('articles' => $articles);
        }

        $data += array(
            'success' => $success,
        );

        return $this->view($data, $statusCode);
    }
}
