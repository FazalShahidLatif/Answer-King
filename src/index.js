import { render } from '@wordpress/element';
import App from './App';
import './index.css';

/**
 * Initialize the Answer King application
 */
const container = document.getElementById('answer-king-app');
if (container) {
    render(<App />, container);
}
