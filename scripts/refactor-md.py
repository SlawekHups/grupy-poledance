#!/usr/bin/env python3
"""
Skrypt refaktoryzacji plików Markdown dla projektu Grupy Poledance
Przenosi wszystkie pliki .md do katalogu docs/ i aktualizuje linki
"""

import os
import re
import json
import shutil
from pathlib import Path
from typing import Dict, List, Tuple, Set
import argparse

class MarkdownRefactorer:
    def __init__(self, project_root: str):
        self.project_root = Path(project_root)
        self.docs_dir = self.project_root / "docs"
        self.report = {
            "moved_files": [],
            "updated_links": [],
            "warnings": [],
            "errors": []
        }
        
    def scan_markdown_files(self) -> List[Path]:
        """Skanuje wszystkie pliki .md w projekcie (bez vendor)"""
        markdown_files = []
        
        for root, dirs, files in os.walk(self.project_root):
            # Pomijamy katalogi vendor, node_modules, .git
            dirs[:] = [d for d in dirs if d not in ['vendor', 'node_modules', '.git', 'docs']]
            
            for file in files:
                if file.endswith('.md'):
                    file_path = Path(root) / file
                    if file_path != self.project_root / "README.md":
                        markdown_files.append(file_path)
        
        return markdown_files
    
    def extract_links(self, content: str) -> List[Tuple[str, str]]:
        """Wyciąga wszystkie linki z treści Markdown"""
        links = []
        
        # Linki do plików .md
        md_links = re.findall(r'\[([^\]]+)\]\(([^)]+\.md[^)]*)\)', content)
        links.extend(md_links)
        
        # Linki do obrazów
        img_links = re.findall(r'!\[([^\]]*)\]\(([^)]+)\)', content)
        links.extend(img_links)
        
        # Linki bez tekstu
        bare_links = re.findall(r'\[([^\]]+)\]\(([^)]+)\)', content)
        links.extend(bare_links)
        
        return links
    
    def calculate_new_path(self, old_path: Path, target_link: str) -> str:
        """Oblicza nową ścieżkę względną do linku po przeniesieniu do docs/"""
        if target_link.startswith(('http://', 'https://', 'mailto:')):
            return target_link  # Linki zewnętrzne bez zmian
        
        # Jeśli link jest względny, obliczamy nową ścieżkę
        if not target_link.startswith('/'):
            # Ścieżka względna do pliku .md
            if target_link.endswith('.md'):
                return f"../{target_link}"
            # Ścieżka do obrazu/zasobu
            else:
                return f"../{target_link}"
        
        return target_link
    
    def update_links_in_file(self, file_path: Path) -> Tuple[str, List[str]]:
        """Aktualizuje linki w pliku Markdown"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            updated_links = []
            
            # Aktualizuj linki do plików .md
            def replace_md_links(match):
                link_text = match.group(1)
                link_url = match.group(2)
                
                if link_url.endswith('.md') and not link_url.startswith(('http://', 'https://')):
                    new_url = self.calculate_new_path(file_path, link_url)
                    updated_links.append(f"{link_url} -> {new_url}")
                    return f"[{link_text}]({new_url})"
                
                return match.group(0)
            
            content = re.sub(r'\[([^\]]+)\]\(([^)]+\.md[^)]*)\)', replace_md_links, content)
            
            # Aktualizuj linki do obrazów
            def replace_img_links(match):
                alt_text = match.group(1)
                img_url = match.group(2)
                
                if not img_url.startswith(('http://', 'https://')):
                    new_url = self.calculate_new_path(file_path, img_url)
                    updated_links.append(f"img: {img_url} -> {new_url}")
                    return f"![{alt_text}]({new_url})"
                
                return match.group(0)
            
            content = re.sub(r'!\[([^\]]*)\]\(([^)]+)\)', replace_img_links, content)
            
            return content, updated_links
            
        except Exception as e:
            self.report["errors"].append(f"Błąd podczas aktualizacji {file_path}: {str(e)}")
            return original_content, []
    
    def refactor_markdown_files(self):
        """Główna funkcja refaktoryzacji"""
        print("🔍 Skanowanie plików Markdown...")
        markdown_files = self.scan_markdown_files()
        
        print(f"📁 Znaleziono {len(markdown_files)} plików Markdown")
        
        # Przenieś pliki do docs/ (jeśli nie są już tam)
        for file_path in markdown_files:
            if not str(file_path).startswith(str(self.docs_dir)):
                # Oblicz nową nazwę pliku
                relative_path = file_path.relative_to(self.project_root)
                new_name = str(relative_path).replace('/', '-').replace('\\', '-')
                new_path = self.docs_dir / new_name
                
                try:
                    # Przenieś plik
                    shutil.move(str(file_path), str(new_path))
                    self.report["moved_files"].append({
                        "from": str(file_path),
                        "to": str(new_path)
                    })
                    print(f"📦 Przeniesiono: {file_path.name} -> docs/{new_name}")
                except Exception as e:
                    self.report["errors"].append(f"Błąd podczas przenoszenia {file_path}: {str(e)}")
        
        # Aktualizuj linki we wszystkich plikach w docs/
        print("🔗 Aktualizacja linków...")
        for file_path in self.docs_dir.glob("*.md"):
            if file_path.is_file():
                updated_content, updated_links = self.update_links_in_file(file_path)
                
                if updated_links:
                    # Zapisz zaktualizowaną treść
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(updated_content)
                    
                    self.report["updated_links"].extend([
                        {"file": str(file_path), "changes": updated_links}
                    ])
                    
                    print(f"✅ Zaktualizowano linki w: {file_path.name}")
        
        # Zapisz raport
        self.save_report()
        
        print("🎉 Refaktoryzacja zakończona!")
        print(f"📊 Raport zapisany w: {self.docs_dir / '_refactor-report.json'}")
    
    def save_report(self):
        """Zapisuje raport refaktoryzacji"""
        report_path = self.docs_dir / "_refactor-report.json"
        
        with open(report_path, 'w', encoding='utf-8') as f:
            json.dump(self.report, f, indent=2, ensure_ascii=False)
    
    def print_summary(self):
        """Wyświetla podsumowanie refaktoryzacji"""
        print("\n📋 PODSUMOWANIE REFAKTORYZACJI")
        print("=" * 50)
        
        print(f"📁 Przeniesione pliki: {len(self.report['moved_files'])}")
        for move in self.report['moved_files']:
            print(f"   {Path(move['from']).name} -> docs/{Path(move['to']).name}")
        
        print(f"🔗 Zaktualizowane pliki: {len(self.report['updated_links'])}")
        for update in self.report['updated_links']:
            print(f"   {Path(update['file']).name}: {len(update['changes'])} zmian")
        
        if self.report['warnings']:
            print(f"⚠️  Ostrzeżenia: {len(self.report['warnings'])}")
            for warning in self.report['warnings']:
                print(f"   {warning}")
        
        if self.report['errors']:
            print(f"❌ Błędy: {len(self.report['errors'])}")
            for error in self.report['errors']:
                print(f"   {error}")

def main():
    parser = argparse.ArgumentParser(description='Refaktoryzacja plików Markdown')
    parser.add_argument('--project-root', default='.', help='Ścieżka do katalogu głównego projektu')
    parser.add_argument('--dry-run', action='store_true', help='Tylko skanowanie bez zmian')
    
    args = parser.parse_args()
    
    refactorer = MarkdownRefactorer(args.project_root)
    
    if args.dry_run:
        print("🔍 Tryb skanowania (bez zmian)")
        markdown_files = refactorer.scan_markdown_files()
        print(f"📁 Znaleziono {len(markdown_files)} plików Markdown:")
        for file_path in markdown_files:
            print(f"   {file_path}")
    else:
        refactorer.refactor_markdown_files()
        refactorer.print_summary()

if __name__ == "__main__":
    main() 