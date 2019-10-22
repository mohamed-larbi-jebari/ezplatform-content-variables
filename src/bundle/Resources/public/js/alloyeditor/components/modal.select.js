import React, { Component } from 'react';
import PropTypes from 'prop-types';

export default class ModalSelect extends Component {
    constructor(props) {
        super(props);
    }

    change(event) {
        this.props.onChange(event.target.value);
    }

    render() {
        return(
            <div className="form-group">
                <label htmlFor={this.props.selectId} className="ez-label">{this.props.title}</label>
                <select id={this.props.selectId} className="form-control" onChange={this.change.bind(this)}>
                    {this.renderOptions()}
                </select>
            </div>
        );
    }

    renderOptions() {
        let options = [];

        this.props.options.forEach(option => {
            options.push(
                <option
                    value={option.id}
                    selected={this.props.selectedOptionId === option.id}
                >
                    {option.name}
                </option>
            );
        });

        return options;
    }
}

ModalSelect.propTypes = {
    title: PropTypes.string.isRequired,
    selectId: PropTypes.string.isRequired,
    options: PropTypes.instanceOf(Map).isRequired,
    selectedOptionId: PropTypes.number,
    onChange: PropTypes.func.isRequired,
};