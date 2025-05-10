import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Link } from '@inertiajs/react';
import { ImageLightbox, SingleImageLightbox } from '@/components/image-lightbox';
import AttachmentsList from '@/components/attachments-list';

const truncateText = (text: string, maxLength: number): string => {
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength) + '…';
};

const getBreadcrumbs = (section: string, slug: string, categorySlug: string | null, categoryTitle: string | null): BreadcrumbItem[] => {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Strona Główna',
            href: '/',
        }
    ];

    if (categorySlug && categoryTitle) {
        breadcrumbs.push({
            title: truncateText(categoryTitle, 35),
            href: '#',
            disabled: true
        });
    }

    breadcrumbs.push({
        title: truncateText(section, 35),
        href: '#',
        disabled: true
    });

    return breadcrumbs;
};

interface Photo {
    id: number;
    image_name: string;
    image_desc: string;
    image_path: string;
    created_at: string;
    updated_at: string;
}

interface Attachment {
    id: number;
    file_name: string;
    file_path: string;
    created_at: string;
    updated_at: string;
}

interface Category {
    id: number;
    title: string;
    slug: string;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    thumbnail: string;
    body: string;
    additional_body?: string;
    map_body?: string;
    active: boolean;
    type: string;
    external: number;
    published_at: string;
    created_at: string;
    updated_at: string;
    user_id: number;
    photos: Photo[];
    attachments: Attachment[];
    map_photo?: Photo;
    categories: Category[];
}

interface ArticleApiResponse {
    articles: Article[];
}

interface ArticlePageProps {
    slug: string;
    categorySlug: string | null;
}

export default function ArticlePage(props: ArticlePageProps) {
    const { slug, categorySlug } = props;
    const [article, setArticle] = useState<Article | null>(null);
    const [categoryTitle, setCategoryTitle] = useState<string | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/articles?slug=${encodeURIComponent(slug)}`)
            .then(response => response.json())
            .then((data: ArticleApiResponse) => {
                if (data.articles && data.articles.length > 0) {
                    const fetchedArticle = data.articles[0];
                    setArticle(fetchedArticle);
                    
                    if (fetchedArticle.categories && fetchedArticle.categories.length > 0) {
                        const matchingCategory = categorySlug ? 
                            fetchedArticle.categories.find(cat => cat.slug === categorySlug) : null;
                            
                        if (matchingCategory) {
                            setCategoryTitle(matchingCategory.title);
                        } else if (fetchedArticle.categories.length > 0) {
                            setCategoryTitle(fetchedArticle.categories[0].title);
                        }
                    }
                } else {
                    setArticle(null);
                }
                setLoading(false);
            })
            .catch(error => {
                setArticle(null);
                setLoading(false);
                console.error('Error fetching article:', error);
            });
    }, [slug, categorySlug]);

    const sectionName = article?.title || slug.replace(/-/g, ' ');

    return (
        <AppLayout breadcrumbs={getBreadcrumbs(sectionName, slug, categorySlug, categoryTitle)}>
            <Head title={`${sectionName ? `${truncateText(sectionName, 35)}` : ''}`}/>
            <div>
                <main className={`bg-background ${article?.map_photo ? 'max-w-screen-xl' : 'max-w-screen-lg'} mx-auto px-4 xl:px-0 pt-8 pb-12 min-h-[75vh]`}>
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie zawartości...</div>
                        </div>
                    ) : !article ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Nie znaleziono artykułu.</div>
                        </div>
                    ) : (
                        <div>
                            {article.type === 'article-with-map' && article.map_photo ? (
                                <div className="flex flex-col md:flex-row gap-8">
                                    <div className="md:w-1/2">
                                        <h1 className="text-3xl font-bold mb-6">{article.title}</h1>
                                        
                                        {article.published_at && (
                                            <div className="mb-4 text-foreground">
                                                Data publikacji: {new Date(article.published_at).toLocaleDateString('pl-PL')}
                                            </div>
                                        )}
                                        
                                        <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.body }}></div>
                                        
                                        {article.additional_body && (
                                            <div className="mt-6 p-4 bg-muted rounded-lg">
                                                <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.additional_body }}></div>
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="md:w-1/2 md:sticky md:top-8">
                                        <SingleImageLightbox 
                                            image={{
                                                image_path: article.map_photo.image_path,
                                                image_desc: article.map_photo.image_desc || 'Mapa lokalizacyjna'
                                            }} 
                                        />
                                        <p className="mt-2 text-sm text-muted-foreground">
                                            Kliknij zdjęcie, aby powiększyć
                                        </p>
                                        
                                        {article.map_body && (
                                            <div className="mt-4 p-4">
                                                <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.map_body }}></div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div>
                                    <h1 className="text-3xl font-bold mb-6">{article.title}</h1>
                                    
                                    {article.published_at && (
                                        <div className="my-4 text-foreground">
                                            Data publikacji: {new Date(article.published_at).toLocaleDateString('pl-PL')}
                                        </div>
                                    )}
                                    
                                    <div className="flex flex-col-reverse md:flex-row gap-8 items-start">
                                        <div className="text-foreground content w-full md:flex-1" dangerouslySetInnerHTML={{ __html: article.body }}></div>
                                        {article.thumbnail && (
                                            <div className="md:max-w-[300px] md:flex-shrink-0">
                                                <SingleImageLightbox 
                                                    image={{
                                                        image_path: article.thumbnail,
                                                        image_desc: article.title
                                                    }} 
                                                />
                                            </div>
                                        )}
                                    </div>
                                    
                                    {article.additional_body && (
                                        <div className="mt-6 p-4 bg-muted rounded-lg">
                                            <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.additional_body }}></div>
                                        </div>
                                    )}
                                </div>
                            )}
                            {article.photos && article.photos.length > 0 && (
                                <div className="mt-8">
                                    <h2 className="text-2xl font-semibold mb-4">Galeria</h2>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        {article.photos.map((photo, index) => (
                                            <ImageLightbox 
                                                key={photo.id} 
                                                images={article.photos} 
                                                initialIndex={index} 
                                            />
                                        ))}
                                    </div>
                                </div>
                            )}
                            
                            <AttachmentsList attachments={article.attachments || []} />
                        </div>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}
