<?php

namespace Sanjab\Traits;

use Illuminate\Support\Facades\App;

/**
 * To get informations about a card from an external controller.
 */
trait InteractsWithCard
{
    /**
     * Get card interaction values.
     *
     * @return array
     */
    protected function getInteractionInfo()
    {
        $request = request();
        $this->validate($request, [
            'controller' => 'required|string',
            'action'     => 'required|string',
            'item'       => 'nullable|numeric',
            'index'      => 'required|numeric',
        ]);
        $controller = $request->input('controller');
        if (! class_exists($controller)) {
            abort(400, 'Controller is not valid.');
        }
        if (! method_exists($controller, $request->input('action'))) {
            abort(400, 'Controller action is not valid.');
        }
        $controllerInsatance = app($request->input('controller'));
        $controllerAction = $request->input('action');
        $controllerActionParameters = [];
        if ($request->filled('item')) {
            $controllerActionParameters['id'] = $request->input('item');
        }
        App::call([$controllerInsatance, $controllerAction], $controllerActionParameters);

        $card = null;
        $cards = $controllerInsatance->getCards();
        if (! isset($cards[$request->input('index')])) {
            abort(400, 'Card '.$request->input('index').' is not valid.');
        }
        $card = $cards[$request->input('index')];

        return [$controllerInsatance, $card];
    }
}
