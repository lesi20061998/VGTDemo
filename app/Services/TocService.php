<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class TocService
{
    public function generate($content)
    {
        $config = setting('toc', []);
        
        if (!($config['enabled'] ?? true)) {
            return ['content' => $content, 'toc' => []];
        }

        $headingLevels = $config['heading_levels'] ?? ['h2', 'h3'];
        $minHeadings = $config['min_headings'] ?? 3;
        
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $toc = [];
        $counter = [];
        
        foreach ($headingLevels as $level) {
            $headings = $xpath->query("//{$level}");
            
            foreach ($headings as $heading) {
                $text = trim($heading->textContent);
                if (empty($text)) continue;
                
                $id = $heading->getAttribute('id');
                if (empty($id)) {
                    $id = $this->generateId($text);
                    $heading->setAttribute('id', $id);
                }
                
                $levelNum = (int) substr($level, 1);
                if (!isset($counter[$levelNum])) {
                    $counter[$levelNum] = 0;
                }
                $counter[$levelNum]++;
                
                // Reset counters for deeper levels
                for ($i = $levelNum + 1; $i <= 6; $i++) {
                    $counter[$i] = 0;
                }
                
                $toc[] = [
                    'id' => $id,
                    'text' => $text,
                    'level' => $levelNum,
                    'number' => $this->getNumber($counter, $levelNum)
                ];
            }
        }
        
        if (count($toc) < $minHeadings) {
            return ['content' => $content, 'toc' => []];
        }
        
        $content = $dom->saveHTML();
        
        return [
            'content' => $content,
            'toc' => $toc,
            'html' => $this->renderToc($toc, $config)
        ];
    }
    
    private function generateId($text)
    {
        $id = strtolower($text);
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
        $id = preg_replace('/\s+/', '-', $id);
        $id = trim($id, '-');
        return $id ?: 'heading-' . uniqid();
    }
    
    private function getNumber($counter, $level)
    {
        $numbers = [];
        for ($i = 2; $i <= $level; $i++) {
            $numbers[] = $counter[$i] ?? 0;
        }
        return implode('.', $numbers);
    }
    
    private function renderToc($toc, $config)
    {
        $title = $config['title'] ?? 'Mục lục';
        $showNumbers = $config['show_numbers'] ?? true;
        $collapsible = $config['collapsible'] ?? true;
        $smoothScroll = $config['smooth_scroll'] ?? true;
        $highlightActive = $config['highlight_active'] ?? true;
        $stickyToc = $config['sticky_toc'] ?? false;
        
        $html = '<div class="toc-container' . ($stickyToc ? ' toc-sticky' : '') . '" id="table-of-contents">';
        $html .= '<div class="toc-header">';
        $html .= '<h3 class="toc-title">' . htmlspecialchars($title) . '</h3>';
        
        if ($collapsible) {
            $html .= '<button class="toc-toggle" onclick="toggleToc()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>';
        }
        
        $html .= '</div>';
        $html .= '<nav class="toc-nav">';
        
        foreach ($toc as $item) {
            $indent = ($item['level'] - 2) * 16;
            $number = $showNumbers ? $item['number'] . '. ' : '';
            $html .= sprintf(
                '<a href="#%s" class="toc-link toc-level-%d" style="padding-left: %dpx" data-target="%s">%s%s</a>',
                $item['id'],
                $item['level'],
                $indent,
                $item['id'],
                $number,
                htmlspecialchars($item['text'])
            );
        }
        
        $html .= '</nav></div>';
        
        if ($smoothScroll || $highlightActive) {
            $html .= $this->renderScript($smoothScroll, $highlightActive);
        }
        
        return $html;
    }
    
    private function renderScript($smoothScroll, $highlightActive)
    {
        $script = '<script>';
        
        if ($smoothScroll) {
            $script .= '
            document.querySelectorAll(".toc-link").forEach(link => {
                link.addEventListener("click", function(e) {
                    e.preventDefault();
                    const target = document.getElementById(this.dataset.target);
                    if (target) {
                        target.scrollIntoView({ behavior: "smooth", block: "start" });
                        history.pushState(null, null, "#" + this.dataset.target);
                    }
                });
            });';
        }
        
        if ($highlightActive) {
            $script .= '
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        document.querySelectorAll(".toc-link").forEach(link => {
                            link.classList.remove("active");
                            if (link.dataset.target === entry.target.id) {
                                link.classList.add("active");
                            }
                        });
                    }
                });
            }, { rootMargin: "-20% 0px -80% 0px" });
            
            document.querySelectorAll("[id]").forEach(el => {
                if (el.tagName.match(/^H[2-6]$/)) observer.observe(el);
            });';
        }
        
        $script .= '
        function toggleToc() {
            document.querySelector(".toc-nav").classList.toggle("hidden");
        }
        </script>';
        
        return $script;
    }
}

