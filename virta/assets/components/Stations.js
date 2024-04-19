import React from 'react';
import axios from 'axios';

export default class Stations extends React.Component {
    constructor(props) {
        super(props);
        this.state = { stations: [], loading: true};
    }

    componentDidMount() {
        this.getStations();
    }

    getStations() {
        axios.get(`/api/v1/stations`).then(stations => {
            this.setState({ stations: stations.data.data, loading: false})
        })
    }

    renderStations(stations) {
        return stations.map(station =>
            <div className="col-md-10 offset-md-1 row-block" key={station.id}>
                <ul id="sortable">
                    <li>
                        <p>name: {station.name}</p>
                        <p>latitude: {station.latitude}</p>
                        <p>longitude: {station.longitude}</p>
                        <p>address: {station.address}</p>
                        <p>company name: {typeof station.company === 'object' ? station.company.name : station.company}</p>
                        {typeof station.company === 'object' && station.company.stations && station.company.stations.length > 0 ? this.renderStations(station.company.stations) : null}
                    </li>
                </ul>
            </div>
        );
    }

    render() {
        const loading = this.state.loading;
        return (
            <div>
                <section className="row-section">
                    <div className="container">
                        {loading ? (
                            <div className={'row text-center'}>
                                <span className="fa fa-spin fa-spinner fa-4x"></span>
                            </div>
                        ) : (
                            <div className={'row'}>
                                {this.renderStations(this.state.stations)}
                            </div>
                        )}
                    </div>
                </section>
            </div>
        )
    }
}