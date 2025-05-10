import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Pagination } from '@/components/pagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Strona Główna',
        href: '/',
    },
    {
        title: 'Aktualności',
        href: '/aktualnosci',
    },
];

interface News {
    id: number;
    title: string;
    slug: string;
    thumbnail: string;
    body: string;
    active: boolean;
    published_at: string;
    created_at: string;
    updated_at: string;
    user_id: number;
}

interface PaginationData {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
}

interface NewsApiResponse {
    articleNews: Record<string, News>;
    pagination: PaginationData;
}

interface NewsProps {
    page?: string | number;
}

export default function News({ page = 1 }: NewsProps) {
    const [news, setNews] = useState<NewsApiResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(typeof page === 'string' ? parseInt(page) : page);

    useEffect(() => {
        const initialPage = typeof page === 'string' ? parseInt(page) : page;
        if (initialPage !== currentPage) {
            setCurrentPage(initialPage);
        }
    }, [page]);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/news?page=${currentPage}&per_page=10`)
            .then(response => response.json())
            .then(data => {
                setNews(data);
                setLoading(false);
            })
            .catch(error => {
                setNews(null);
                setLoading(false);
                console.error('Error fetching news data:', error);
            });
    }, [currentPage]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Aktualności"/>
            <div>
                <main className="bg-background max-w-screen-xl mx-auto px-4 xl:px-0 pt-8 pb-12">
                    <h1 className="text-3xl font-bold mb-6">Aktualności</h1>
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie...</div>
                        </div>
                    ) : !news || Object.keys(news.articleNews).length === 0 ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Brak artykułów.</div>
                        </div>
                    ) : (
                        <div>
                            <div className="space-y-8 mb-8">
                                {Object.values(news.articleNews).map((article) => {
                                    function stripHtml(html: string) {
                                        const htmlWithSpaces = html
                                            .replace(/<br\s*\/?>/gi, ' ')
                                            .replace(/<\/p><p>/gi, ' ')
                                            .replace(/<\/div><div>/gi, ' ')
                                            .replace(/<\/h[1-6]><h[1-6]>/gi, ' ')
                                            .replace(/<\/li><li>/gi, ' ');
                                            
                                        const div = document.createElement('div');
                                        div.innerHTML = htmlWithSpaces;
                                        
                                        return (div.textContent || div.innerText || '')
                                            .replace(/\s+/g, ' ')
                                            .trim();
                                    }
                                    const plainText = stripHtml(article.body);
                                    const shortText = plainText.length > 200 
                                        ? plainText.slice(0, 200).replace(/\s+$/, '') + '…' 
                                        : plainText;
                                    return (
                                        <div key={article.id} className="p-6 bg-card rounded-lg shadow-sm">
                                            <h2 className="text-2xl font-bold mb-2">{article.title}</h2>
                                            <div className="mb-2 text-muted-foreground">Data publikacji: {new Date(article.published_at).toLocaleDateString('pl-PL')}</div>
                                            <div className="text-foreground mb-4">{shortText}</div>
                                            <a
                                                href={`/aktualnosci/${article.slug}`}
                                                className="inline-block text-primary dark:text-accent hover:underline font-semibold contrast:font-bold"
                                            >
                                                Czytaj więcej
                                            </a>
                                        </div>
                                    );
                                })}
                            </div>
                            
                            {news.pagination && news.pagination.total > 10 && (
                                <Pagination 
                                    currentPage={currentPage}
                                    lastPage={news.pagination.last_page}
                                    onPageChange={(page) => {
                                        setCurrentPage(page);
                                        window.scrollTo(0, 0);
                                        
                                        if (page === 1) {
                                            window.history.pushState({}, '', '/aktualnosci');
                                        } else {
                                            window.history.pushState({}, '', `/aktualnosci?strona=${page}`);
                                        }
                                    }}
                                />
                            )}
                        </div>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}
