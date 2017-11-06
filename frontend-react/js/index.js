import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route, Switch } from 'react-router-dom';

import ProductById from './component/ProductById.react';
import ProductForm from './component/ProductForm.react';
import ProductList from './component/ProductList.react';

const PORT = 8080;

ReactDOM.render(
    <BrowserRouter>
        <Switch>
            <Route exact path="/" component={() => <ProductForm port={PORT} />} />
            <Route exact path="/list" component={() => <ProductList port={PORT} />} />
            <Route exact path="/:id" component={(route) => <ProductById id={Number(route.match.params.id)} port={PORT} />} />
        </Switch>
    </BrowserRouter>,
    document.getElementById('app')
);
