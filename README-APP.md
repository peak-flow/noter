# Note Processor

A Laravel application that imports notes from local folders, copies them safely, and uses AI to organize them into categories with concise summaries.

## Features

- **Safe File Import**: Copies notes to app storage, leaving originals untouched
- **Multi-format Support**: Reads TXT, PDF, Markdown, and RTF files
- **AI-Powered Organization**: Automatically categorizes and summarizes using OpenAI
- **Web Interface**: Import folders, browse categories, search notes
- **Progress Tracking**: Monitor processing status for each imported folder
- **Preserves Metadata**: Maintains original file creation and modification dates

## Installation

1. Clone the repository
2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up your OpenAI API key in `.env`:
```
OPENAI_API_KEY=your-openai-api-key-here
```

5. Run migrations:
```bash
php artisan migrate
```

## Quick Start

1. **Start the server**:
```bash
php artisan serve
```

2. **Import a folder**:
   - Visit `http://localhost:8000/folders/create`
   - Enter the path to your notes folder (e.g., `/tmp/test-notes`)
   - Click "Import Folder"

3. **Process notes with AI**:
```bash
# Process all unprocessed notes
php artisan notes:process

# Process specific folder
php artisan notes:process --folder=1

# Limit processing
php artisan notes:process --limit=5
```

4. **Browse results**:
   - Visit `http://localhost:8000` to see categorized notes
   - Search and view AI-generated summaries

## Test Data

Sample notes are available at `/tmp/test-notes/` including:
- Meeting notes
- Recipe (Markdown)
- Programming tutorial
- Garden planning
- Book review

## Usage

### Importing Folders

**Web Interface (Recommended)**:
1. Go to `/folders/create`
2. Enter local folder path
3. Files are safely copied preserving metadata

**Command Line** (legacy):
```bash
php artisan notes:process --path=/path/to/folder
```

### Processing Notes

```bash
# Process all imported unprocessed notes
php artisan notes:process

# Process specific folder by ID
php artisan notes:process --folder=1

# Process with limits
php artisan notes:process --limit=10
```

### Web Interface

- **Dashboard**: View statistics and categories
- **Folders**: Manage imported folders and track progress
- **Search**: Find notes across all categories
- **Note Details**: View summaries, key points, and original content

## Supported File Formats

- `.txt` - Plain text files
- `.md` / `.markdown` - Markdown files  
- `.pdf` - PDF documents
- `.rtf` - Rich text format files

## How It Works

1. **Import**: Files are copied from source folder to app storage
2. **Analysis**: AI analyzes content to determine categories/subcategories
3. **Summarization**: AI creates concise summaries and key points
4. **Organization**: Notes are organized in searchable web interface

## Database Structure

- **folders**: Track imported folders and processing progress
- **notes**: Store copied files with original metadata
- **categories/subcategories**: AI-generated organization structure
- **processed_notes**: Store summaries and analysis results

## Configuration

Key settings in `.env`:

- `OPENAI_API_KEY`: Your OpenAI API key (required)
- `OPENAI_ORGANIZATION`: Optional OpenAI organization ID

## Requirements

- PHP 8.1+
- Composer
- SQLite/MySQL
- OpenAI API key
- `pdftotext` utility (for PDF support)

## License

MIT