import { Controller } from '@hotwired/stimulus';
import React from 'react';
import { createRoot } from 'react-dom/client';
import {BrowserRouter} from 'react-router-dom';

import Stations from "../components/Stations";

export default class extends Controller {

    connect() {
        createRoot(this.element).render(
            <BrowserRouter>
                <Stations />
            </BrowserRouter>,
        )
    }
}
