import React, { useState } from 'react';
import OrganizationStatisticsModal from './OrganizationStatisticsModal';

const StatisticsOrgCards = ({ organizations }) => {
    const [statsOpen, setStatsOpen] = useState(false);
    const [statsOrgId, setStatsOrgId] = useState(null);

    const handleOpen = (orgId) => {
        setStatsOrgId(orgId);
        setStatsOpen(true);
    };

    return (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {organizations.length === 0 ? (
                    <p className="col-span-full text-center text-muted-foreground py-8">No organizations available.</p>
                ) : (
                    organizations.map((org) => (
                        <button
                            key={org.id}
                            onClick={() => handleOpen(org.id)}
                            className="rounded-lg border border-white/10 bg-background p-4 hover:border-blue-500/40 hover:bg-blue-500/5 transition-all hover:-translate-y-1 hover:shadow-md group text-left w-full"
                        >
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="font-semibold text-foreground group-hover:text-blue-400 transition-colors">
                                        {org.name}
                                    </p>
                                    {org.acronym && (
                                        <p className="text-sm text-muted-foreground">{org.acronym}</p>
                                    )}
                                    {org.campus && (
                                        <p className="text-xs text-blue-400 mt-1">{org.campus}</p>
                                    )}
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    className="w-5 h-5 text-muted-foreground group-hover:text-blue-400 transition-colors mt-0.5 flex-shrink-0"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>
                    ))
                )}
            </div>

            <OrganizationStatisticsModal
                open={statsOpen}
                onOpenChange={setStatsOpen}
                orgId={statsOrgId}
            />
        </>
    );
};

export default StatisticsOrgCards;
