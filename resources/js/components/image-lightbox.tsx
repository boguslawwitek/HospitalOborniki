import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogTrigger, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight, X } from 'lucide-react';

interface ImageLightboxProps {
  images: Array<{
    id: number;
    image_path: string;
    image_desc?: string;
  }>;
  initialIndex?: number;
}

export function ImageLightbox({ images, initialIndex = 0 }: ImageLightboxProps) {
  const [open, setOpen] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(initialIndex);
  
  useEffect(() => {
    setCurrentIndex(initialIndex);
  }, [initialIndex]);
  
  useEffect(() => {
    if (open) {
      setCurrentIndex(initialIndex);
    }
  }, [open, initialIndex]);
  
  const handlePrevious = (e: React.MouseEvent) => {
    e.stopPropagation();
    setCurrentIndex((prev) => (prev === 0 ? images.length - 1 : prev - 1));
  };
  
  const handleNext = (e: React.MouseEvent) => {
    e.stopPropagation();
    setCurrentIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
  };
  
  // Handle keyboard navigation
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (!open) return;
      
      if (e.key === 'ArrowLeft') {
        setCurrentIndex((prev) => (prev === 0 ? images.length - 1 : prev - 1));
      } else if (e.key === 'ArrowRight') {
        setCurrentIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
      } else if (e.key === 'Escape') {
        setOpen(false);
      }
    };
    
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [open, images.length]);
  
  if (!images.length) return null;
  
  const currentImage = images[currentIndex];
  
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <div className="cursor-pointer overflow-hidden rounded-lg shadow-md hover:opacity-90 transition-opacity">
          <img 
            src={images[initialIndex].image_path} 
            alt={images[initialIndex].image_desc || 'Zdjęcie'} 
            className="w-full h-64 object-cover" 
            loading="lazy"
          />
        </div>
      </DialogTrigger>
      <DialogContent className="sm:max-w-4xl p-0 bg-transparent border-none shadow-none [&>button]:hidden">
        <DialogTitle className="sr-only">{currentImage.image_desc || 'Zdjęcie'}</DialogTitle>
        <DialogDescription className="sr-only">Przeglądarka zdjęć</DialogDescription>
        <div className="fixed inset-0 bg-black/90 flex items-center justify-center">
          <div className="relative flex items-center justify-center w-full max-w-4xl">
            <div className="relative">
              <img 
                src={currentImage.image_path} 
                alt={currentImage.image_desc || 'Zdjęcie'} 
                className="max-h-[80vh] max-w-[90vw] object-contain rounded-lg"
                style={{ aspectRatio: 'auto' }}
              />
              
              <Button 
                variant="outline" 
                size="icon" 
                className="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white dark:bg-neutral-800 dark:hover:bg-neutral-700"
                onClick={handlePrevious}
              >
                <ChevronLeft className="h-6 w-6" />
              </Button>
              
              <Button 
                variant="outline" 
                size="icon" 
                className="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white dark:bg-neutral-800 dark:hover:bg-neutral-700"
                onClick={handleNext}
              >
                <ChevronRight className="h-6 w-6" />
              </Button>
            
              <Button 
                variant="outline" 
                size="icon" 
                className="absolute top-2 right-2 z-10 bg-white/80 hover:bg-white dark:bg-neutral-800 dark:hover:bg-neutral-700"
                onClick={() => setOpen(false)}
              >
                <X className="h-4 w-4" />
              </Button>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

export function SingleImageLightbox({ image }: { image: { image_path: string; image_desc?: string } }) {
  const [open, setOpen] = useState(false);
  
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <div className="cursor-pointer overflow-hidden rounded-lg shadow-md hover:opacity-90 transition-opacity">
          <img 
            src={image.image_path} 
            alt={image.image_desc || 'Zdjęcie'} 
            className="w-full object-contain" 
            style={{ height: 'auto', maxHeight: '400px' }}
            loading="lazy"
          />
        </div>
      </DialogTrigger>
      <DialogContent className="sm:max-w-4xl p-0 bg-transparent border-none shadow-none [&>button]:hidden">
        <DialogTitle className="sr-only">{image.image_desc || 'Zdjęcie'}</DialogTitle>
        <DialogDescription className="sr-only">Przeglądarka zdjęć</DialogDescription>
        <div className="fixed inset-0 bg-black/90 flex items-center justify-center">
          <div className="relative">
            <img 
              src={image.image_path} 
              alt={image.image_desc || 'Zdjęcie'} 
              className="max-h-[80vh] max-w-[90vw] object-contain rounded-lg"
              style={{ aspectRatio: 'auto' }}
            />
            <Button 
              variant="outline" 
              size="icon" 
              className="absolute top-4 right-4 z-10 bg-white/80 hover:bg-white dark:bg-neutral-800 dark:hover:bg-neutral-700"
              onClick={() => setOpen(false)}
            >
              <X className="h-4 w-4" />
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
