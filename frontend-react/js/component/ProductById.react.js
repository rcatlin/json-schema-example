import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Request from 'request';

import Header from './Header.react';
import Product from './Product.react';

class ProductById extends Component {
    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            product: null
        };
    }

    componentDidMount() {
        Request.get(
            {
                json: true,
                uri: 'http://localhost:' + this.props.port + '/' + this.props.id
            },
            (err, resp, body) => {
                if (resp === undefined) this.setState({loading: false});
                else if (resp.statusCode !== 200) this.stateState({loading: false});
                else this.setState({ loading: false, product: body });
            }
        )
    }

    render() {
        var panelBody;
        if (this.state.loading) panelBody = 'Loading...';
        else if (this.state.product === null) panelBody = 'Failed to load Product';
        else panelBody = <Product {...this.state.product} />;

        return (
            <div>
                <Header />
                <div className="panel panel-default">
                    <div className="panel-body">{panelBody}</div>
                </div>
            </div>
        )
    }
}

ProductById.propTypes = {
    id: PropTypes.number.isRequired,
    port: PropTypes.number.isRequired
};

export default ProductById;
