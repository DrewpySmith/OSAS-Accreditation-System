import React from 'react';
import ReactDOM from 'react-dom/client';
import UploadModal from './components/UploadModal';
import OrganizationsList from './components/OrganizationsList';
import '../css/app.css';

// Guard against double-initialization (can happen with Vite chunks)
if (window.__osasInitialized) {
    console.log('OSAS already initialized, skipping.');
} else {
    window.__osasInitialized = true;

    // Simple error boundary component
    class ErrorBoundary extends React.Component {
        constructor(props) {
            super(props);
            this.state = { hasError: false, error: null };
        }
        static getDerivedStateFromError(error) {
            return { hasError: true, error };
        }
        componentDidCatch(error, info) {
            console.error('React component error:', error, info);
        }
        render() {
            if (this.state.hasError) {
                return (
                    <div style={{ padding: '2rem', color: '#ef4444', background: '#1a1a2e', borderRadius: '8px', margin: '1rem' }}>
                        <strong>Component Error:</strong> {String(this.state.error)}
                    </div>
                );
            }
            return this.props.children;
        }
    }

    // Mount UploadModal (Organization side)
    const modalRoot = document.getElementById('react-modals');
    if (modalRoot && !modalRoot._reactRootContainer) {
        const documentTypes = {
            'application_letter': 'Application Letter',
            'officer_list': 'Lists of Officers',
            'commitment_forms': 'Commitment Forms',
            'constitution_bylaws': 'Constitution and By-Laws',
            'org_structure': 'Organizational Structure',
            'calendar_activities': 'Plan and Calendar of Activities',
            'financial_report': 'Audited Financial Report',
            'program_expenditures': 'Program of Expenditures',
            'accomplishment_report': 'Accomplishment Reports',
            'other': 'Other Documents'
        };

        ReactDOM.createRoot(modalRoot).render(
            <ErrorBoundary>
                <UploadModal documentTypes={documentTypes} />
            </ErrorBoundary>
        );
    }

    // Mount OrganizationsList (Admin side)
    const listRoot = document.getElementById('react-organizations-list');
    if (listRoot && !listRoot._reactRootContainer) {
        const propsRaw = listRoot.getAttribute('data-props');
        let props = {};
        if (propsRaw) {
            try {
                props = JSON.parse(propsRaw);
            } catch (e) {
                console.error('Failed to parse OrganizationsList props:', e);
            }
        }

        ReactDOM.createRoot(listRoot).render(
            <ErrorBoundary>
                <OrganizationsList {...props} />
            </ErrorBoundary>
        );
    }

    console.log('OSAS Modern Frontend Initialized');
}
