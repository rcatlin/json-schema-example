import React, { Component } from 'react';
import Request from 'request';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import Header from './Header.react';

class ProductList extends Component {
    constructor(props) {
        super(props);
        this.state = { products: [] };
    }

    componentDidMount() {
        Request.get({uri: 'http://localhost:' + this.props.port, json: true}, (err, resp, body) => {
            if (!resp) return;

            this.setState({products: resp.body});
        });
    }

    render() {
        var products = [],
            idx,
            product;
        for (idx in this.state.products) {
            product = this.state.products[idx];
            products.push(
                <li key={product.id} className="list-group-item">
                    <Link to={'/' + product.id}>{product.name}{' '}({product.brand})</Link>
                </li>
            );
        }

        return (
            <div>
                <Header />
                <div>
                    <h1>Products List</h1>
                </div>
                <div>
                    <ul className="list-group">{products}</ul>
                </div>
            </div>
        )
    }
}

ProductList.propTypes = {
    port: PropTypes.number.isRequired
};

export default ProductList;
