import React, { Component } from 'react';
import PropTypes from 'prop-types';

class Product extends Component {
    render() {
        return (
            <div>
                <h1>{this.props.name}{' (id: '}{this.props.id}{')'}</h1>
                <div>
                    <strong>Description</strong>
                    {' '}{this.props.description}
                </div>
                <div>
                    <strong>Brand</strong>
                    {' '}{this.props.brand}
                </div>
                <div>
                    <strong>Price</strong>
                    {' '}{this.props.price}
                </div>
            </div>
        );
    }
}

Product.propTypes = {
    id: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    description: PropTypes.string,
    brand: PropTypes.string.isRequired
};

export default Product;
