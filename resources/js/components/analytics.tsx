import { useEffect } from 'react';

interface AnalyticsProps {
    url?: string;
    websiteId?: string;
}

export default function Analytics({ url, websiteId }: AnalyticsProps) {
    useEffect(() => {
        if (!url || !websiteId) return;

        const script = document.createElement('script');
        script.src = url;
        script.defer = true;
        script.setAttribute('data-website-id', websiteId);
        
        document.head.appendChild(script);

        return () => {
            document.head.removeChild(script);
        };
    }, [url, websiteId]);

    return null;
}
