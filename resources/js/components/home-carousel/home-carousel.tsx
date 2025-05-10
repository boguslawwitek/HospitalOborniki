import * as React from "react";
import { Link, Head } from '@inertiajs/react';
import Autoplay from "embla-carousel-autoplay";

import { Card, CardContent } from "@/components/ui/card"
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel"

const SLIDES_COUNT = 13;
const AUTOPLAY_DELAY = 4000;

interface HomepageData {
    title?: string;
    content?: string;
    photo?: string;
    carouselPhotos: Array<{
        name: string;
        photo: string;
        description: string;
        url: string;
        external: boolean;
    }>;
}

interface CarouselProps extends React.HTMLAttributes<HTMLElement> {}

export default function HomeCarousel({ className, ...props }: CarouselProps) {
    const [homepageData, setHomepageData] = React.useState<HomepageData | null>(null);

    React.useEffect(() => {
        fetch('/api/homepage')
            .then(response => response.json())
            .then(data => setHomepageData(data))
            .catch(error => console.error('Error fetching carousel photos:', error));
    }, []);

    return (
        <div className="w-full max-w-screen-sm md:max-w-screen-lg lg:max-w-screen-xl mx-auto px-12">
            <Carousel
                className="mt-8 mb-8 select-none"
        plugins={[
          Autoplay({
            delay: AUTOPLAY_DELAY,
          }),
        ]}
      >
        <CarouselContent className="-ml-1">
          {homepageData?.carouselPhotos?.map((photo, index) => (
            <CarouselItem key={index} className="pl-1 basis-3/3 md:basis-1/3 lg:basis-1/5">
              <div className="p-1">
                <Card className="p-0">
                  <CardContent className="flex aspect-square items-center justify-center p-0">
                    {photo.external ? (
                        <a href={photo.url} target="_blank" rel="noopener noreferrer" title={photo.name} className="cursor-pointer">
                            <img src={photo.photo} alt={photo.description} className="h-full object-cover rounded-lg" />
                        </a>
                    ) : (
                        <Link href={photo.url} title={photo.name} className="cursor-pointer">
                            <img src={photo.photo} alt={photo.description} className="h-full object-cover rounded-lg" />
                        </Link>
                    )}
                  </CardContent>
                </Card>
              </div>
            </CarouselItem>
          ))}
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>
    </div>
  );
}
