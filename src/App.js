import { useState, useEffect } from '@wordpress/element';
import { SearchControl, Spinner, Button, SelectControl } from '@wordpress/components';
import KeywordMap from './components/KeywordMap';

const App = () => {
    const [keyword, setKeyword] = useState('');
    const [results, setResults] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [clients, setClients] = useState([]);
    const [selectedClient, setSelectedClient] = useState('');
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        // Fetch clients on mount
        fetch('/wp-json/answer-king/v1/clients')
            .then(res => res.json())
            .then(data => {
                const options = [{ label: 'Select Client...', value: '' }];
                data.forEach(client => {
                    options.push({ label: client.name, value: client.id });
                });
                setClients(options);
            });
    }, []);

    const handleSearch = async () => {
        if (!keyword) return;
        
        setIsLoading(true);
        try {
            const response = await fetch(`/wp-json/answer-king/v1/research?q=${encodeURIComponent(keyword)}`);
            const data = await response.json();
            setResults(data);
        } catch (error) {
            console.error('Search failed:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSave = async () => {
        if (!results || !keyword) return;

        setIsSaving(true);
        try {
            const response = await fetch('/wp-json/answer-king/v1/save-map', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    keyword: keyword,
                    data: results,
                    client_id: selectedClient
                })
            });
            const resData = await response.json();
            if (resData.success) {
                alert('Ecosystem Map saved to SaaSSkul Repository!');
            }
        } catch (error) {
            console.error('Save failed:', error);
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <div className="ak-app-container">
            <div className="ak-header">
                <h2>Keyword Ecosystem Builder</h2>
                <div className="ak-search-row">
                    <SelectControl
                        label="Client Context"
                        value={selectedClient}
                        options={clients}
                        onChange={(val) => setSelectedClient(val)}
                    />
                    <SearchControl
                        label="Seed Keyword"
                        value={keyword}
                        onChange={(val) => setKeyword(val)}
                        onNext={handleSearch}
                    />
                    <Button isPrimary onClick={handleSearch} disabled={isLoading}>
                        {isLoading ? <Spinner /> : 'Visualize Ecosystem'}
                    </Button>
                </div>
            </div>

            <div className="ak-toolbar" style={{ marginBottom: '15px', display: 'flex', justifyContent: 'flex-end' }}>
                {results && (
                    <Button isSecondary onClick={handleSave} disabled={isSaving}>
                        {isSaving ? <Spinner /> : 'Save Map to Repository'}
                    </Button>
                )}
            </div>

            <div className="ak-map-viewport">
                {results ? (
                    <KeywordMap data={results} rootName={keyword} />
                ) : (
                    <div className="ak-placeholder">
                        <div className="ak-placeholder-icon">🧠</div>
                        <h3>Ready to Map?</h3>
                        <p>Enter a seed keyword to visualize the semantic ecosystem.</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default App;
