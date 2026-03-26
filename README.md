# Noter

AI-powered note management and categorization system. Import folders of documents, process them with OpenAI for automatic categorization and summarization, and browse the results through a clean web interface.

## How It Works

1. **Import** a folder of documents (TXT, PDF, Markdown, RTF) via the web UI
2. **Process** imported notes with an Artisan command — each note is sent to OpenAI for categorization and summarization
3. **Browse** organized notes by AI-generated categories, view summaries, and search across your collection

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Database:** SQLite (WAL mode for concurrency)
- **AI:** OpenAI API (gpt-3.5-turbo) via [openai-php/laravel](https://github.com/openai-php/laravel)
- **PDF Extraction:** [spatie/pdf-to-text](https://github.com/spatie/pdf-to-text)
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js

## Setup

```bash
# Clone and install
git clone https://github.com/peak-flow/noter.git
cd noter
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate
```

Add your OpenAI API key to `.env`:

```
OPENAI_API_KEY=your-key-here
```

Run migrations and start the dev server:

```bash
php artisan migrate
composer dev
```

This starts the web server, queue worker, log viewer, and Vite dev server concurrently.

## Usage

### Web Interface

- **Dashboard** (`/`) — Browse categories with note counts
- **Folders** (`/folders`) — Manage imported document folders
- **Import** (`/folders/create`) — Import a new folder by providing its path
- **Search** (`/search`) — Search across processed note titles and summaries

### Processing Notes

After importing a folder, process the notes via CLI:

```bash
# Process all unprocessed notes (default limit: 10)
php artisan notes:process

# Process a specific folder
php artisan notes:process --folder=1

# Process with custom limit
php artisan notes:process --limit=50

# Process from a directory path directly
php artisan notes:process --path=/path/to/notes
```

Each note goes through two AI passes:
1. **Categorization** — assigns a category and optional subcategory
2. **Summarization** — generates a title, summary, and key points

## Architecture

```
app/
  Console/Commands/     ProcessNotes — CLI command for AI processing
  Http/Controllers/     NotesController, FoldersController
  Models/               User, Category, Subcategory, Note, ProcessedNote, Folder
  Services/
    FileReaderService   — Multi-format file reading (TXT, MD, PDF, RTF)
    LLMService          — OpenAI API integration (categorize + summarize)
    FolderImportService — Folder import orchestration
```

## Supported File Formats

- Plain text (`.txt`, `.text`)
- Markdown (`.md`, `.markdown`)
- PDF (`.pdf`) — requires `pdftotext` binary (poppler-utils)
- Rich Text (`.rtf`)

## License

MIT
