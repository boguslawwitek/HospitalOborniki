import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import axios from 'axios';
import { SingleImageLightbox, ImageLightbox } from '@/components/image-lightbox';
import AttachmentsList from '@/components/attachments-list';

interface Photo {
  id: number;
  image_name: string;
  image_path: string;
  image_size: number;
  image_type: string;
  created_at: string;
  updated_at: string;
}

interface Attachment {
  id: number;
  file_name: string;
  file_path: string;
  file_size: number;
  file_type: string;
  created_at: string;
  updated_at: string;
}

interface ProjectType {
  id: number;
  title: string;
  slug: string;
  created_at: string;
  updated_at: string;
}

interface Project {
  id: number;
  title: string;
  slug: string;
  body: string;
  logo: string;
  active: boolean;
  published_at: string;
  created_at: string;
  updated_at: string;
  user_id: number;
  projectTypes: ProjectType[];
  photos?: Photo[];
  attachments?: Attachment[];
}

interface ProjectApiResponse {
  project: Project;
  projectType: ProjectType;
}

interface ProjectDetailsProps {
  typeSlug: string;
  projectSlug: string;
}

export default function ProjectDetails({ typeSlug, projectSlug }: ProjectDetailsProps) {
  const [projectData, setProjectData] = useState<ProjectApiResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    fetch(`/api/projects/${encodeURIComponent(typeSlug)}/${encodeURIComponent(projectSlug)}`)
      .then(response => response.json())
      .then(data => {
        setProjectData(data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Error fetching project details:', error);
        setProjectData(null);
        setLoading(false);
      });
  }, [typeSlug, projectSlug]);

  const truncateText = (text: string, maxLength: number): string => {
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength) + '…';
  };

  const getBreadcrumbs = (): BreadcrumbItem[] => [
    {
      title: 'Strona Główna',
      href: '/',
    },
    {
      title: 'Projekty',
      href: '#',
      disabled: true,
    },
    {
      title: projectData?.projectType?.title || typeSlug.replace(/-/g, ' '),
      href: `/projekty/${typeSlug}`,
    },
    {
      title: truncateText(projectData?.project?.title || projectSlug.replace(/-/g, ' '), 35),
      href: `/projekty/${typeSlug}/${projectSlug}`,
    },
  ];

  return (
    <AppLayout breadcrumbs={getBreadcrumbs()}>
      <Head title={`${truncateText(projectData?.project?.title || 'Projekt', 35)}`} />
      <div>
        <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12 min-h-[75vh]">
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Wczytywanie zawartości...</div>
            </div>
          ) : !projectData || !projectData.project ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Nie znaleziono projektu.</div>
            </div>
          ) : (
            <div className="mb-8">
              {projectData.project.logo && (
                <div className="flex justify-center mb-8">
                  <img 
                    src={projectData.project.logo} 
                    alt={projectData.project.title} 
                    className="max-h-40 object-contain"
                  />
                </div>
              )}
              
              <div className="max-w-3xl mx-auto">
                <h1 className="text-3xl font-bold mb-4 text-center">{projectData.project.title}</h1>
                
                {projectData.project.projectTypes && projectData.project.projectTypes.length > 0 && (
                  <div className="flex flex-wrap justify-center gap-2 mb-4">
                    {projectData.project.projectTypes.map(type => (
                      <Link
                        key={type.id}
                        href={`/projekty/${type.slug}`}
                        className="px-3 py-1 text-sm bg-muted rounded-full hover:bg-muted/80"
                      >
                        {type.title}
                      </Link>
                    ))}
                  </div>
                )}
                
                <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: projectData.project.body }}></div>
              </div>
              
              {projectData.project.photos && projectData.project.photos.length > 0 && (
                <div className="mt-8 max-w-4xl mx-auto">
                  <h2 className="text-2xl font-semibold mb-4 text-center">Galeria</h2>
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {projectData.project.photos.map((photo, index) => (
                      <ImageLightbox 
                        key={photo.id} 
                        images={projectData.project.photos as Array<{
                          id: number;
                          image_path: string;
                          image_desc?: string;
                        }>} 
                        initialIndex={index} 
                      />
                    ))}
                  </div>
                </div>
              )}
              
              <AttachmentsList attachments={projectData.project.attachments || []} />
              
              <div className="mt-8 pt-4 border-t border-border max-w-3xl mx-auto text-center">
                <Link
                  href={`/projekty/${typeSlug}`}
                  className="inline-flex items-center text-primary hover:underline"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                  Powrót do listy projektów
                </Link>
              </div>
            </div>
          )}
        </main>
      </div>
    </AppLayout>
  );
}
