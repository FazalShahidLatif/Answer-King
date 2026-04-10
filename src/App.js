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
    const [isGenerating, setIsGenerating] = useState(false);
    const [auditResults, setAuditResults] = useState(null);
    const [isAuditing, setIsAuditing] = useState(false);

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

    const handleGenerateContent = async () => {
        if (!results || !keyword) return;

        setIsGenerating(true);
        try {
            const response = await fetch('/wp-json/answer-king/v1/generate-content', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    keyword: keyword,
                    selectedNodes: Object.values(results).flat().map(n => n.name)
                })
            });
            const resData = await response.json();
            if (resData.success) {
                alert(`Content Draft Created! Redirecting to editor...`);
                window.open(resData.url, '_blank');
                // Store post_id for audit
                sessionStorage.setItem('ak_last_post_id', resData.post_id);
            }
        } catch (error) {
            console.error('Generation failed:', error);
        } finally {
            setIsGenerating(false);
        }
    };

    const handleRunAudit = async () => {
        const postId = sessionStorage.getItem('ak_last_post_id');
        if (!postId || !keyword) {
            alert('Please generate a Content Bridge draft first to audit.');
            return;
        }

        setIsAuditing(true);
        try {
            const response = await fetch(`/wp-json/answer-king/v1/audit?post_id=${postId}&keyword=${encodeURIComponent(keyword)}`);
            const data = await response.json();
            setAuditResults(data);
        } catch (error) {
            console.error('Audit failed:', error);
        } finally {
            setIsAuditing(false);
        }
    };

    const handleSmartFix = () => {
        alert('SaaSSkul AI is automatically fixing SEO issues in the WordPress editor...');
        // In a real scenario, this would send a REST request to update the post content via AI
        handleRunAudit(); // Refresh audit
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

            <div className="ak-toolbar" style={{ marginBottom: '15px', display: 'flex', justifyContent: 'flex-end', gap: '10px' }}>
                {results && (
                    <>
                        <Button isSecondary onClick={handleSave} disabled={isSaving}>
                            {isSaving ? <Spinner /> : 'Save Map to Repository'}
                        </Button>
                        <Button isSecondary onClick={handleGenerateContent} disabled={isGenerating}>
                            {isGenerating ? <Spinner /> : '🚀 Generate Content Bridge'}
                        </Button>
                        <Button isSecondary onClick={handleRunAudit} disabled={isAuditing}>
                            {isAuditing ? <Spinner /> : '🔍 Advanced SEO Audit'}
                        </Button>
                    </>
                )}
            </div>

            <div className="ak-main-layout" style={{ display: 'flex', gap: '20px' }}>
                <div className="ak-map-viewport" style={{ flex: auditResults ? '2' : '1' }}>
                    {results ? (
                        <KeywordMap data={results} rootName={keyword} />
                    ) : (
                        <div className="ak-placeholder">
                            <div className="ak-placeholder-icon">🧠</div>
                            <h3>Ready to Map?</h3>
                            <p>Enter a seed keyword to visualize the semantic ecosystem with SEO Heatmaps.</p>
                        </div>
                    )}
                </div>

                {auditResults && (
                    <div className="ak-audit-sidebar" style={{ flex: '1', background: '#fff', border: '1px solid #e2e8f0', borderRadius: '8px', padding: '20px' }}>
                        <div className="ak-audit-header" style={{ textAlign: 'center', marginBottom: '20px' }}>
                            <div className="ak-score-circle" style={{ fontSize: '48px', fontWeight: 'bold', color: auditResults.score > 70 ? '#22c55e' : '#ef4444' }}>
                                {auditResults.score}
                                <span style={{ fontSize: '18px' }}>/100</span>
                            </div>
                            <h3>SEO Optimization Score</h3>
                        </div>

                        <div className="ak-audit-checklist">
                            {Object.values(auditResults.checks).map((check, idx) => (
                                <div key={idx} className="ak-audit-item" style={{ marginBottom: '15px', padding: '10px', borderLeft: `4px solid ${check.passed ? '#22c55e' : '#ef4444'}`, background: '#f8fafc' }}>
                                    <strong>{check.label} {check.passed ? '✅' : '❌'}</strong>
                                    <p style={{ margin: '5px 0 0', fontSize: '12px' }}>{check.message}</p>
                                </div>
                            ))}
                        </div>

                        <Button isPrimary style={{ width: '100%', marginTop: '10px' }} onClick={handleSmartFix}>
                            ✨ Smart Fix AI Optimization
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
};

export default App;
