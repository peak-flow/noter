# Service Architecture Diagram

## Note Processor Service Layer

This diagram shows the service layer architecture and how services interact with each other and external APIs.

```mermaid
graph TB
    subgraph "External Systems"
        FS[File System]
        OPENAI[OpenAI API]
    end

    subgraph "Service Layer"
        FRS[FileReaderService]
        FIS[FolderImportService]
        LLM[LLMService]
    end

    subgraph "Data Layer"
        DB[(Database)]
        STORAGE[Laravel Storage]
    end

    subgraph "Controllers"
        FC[FoldersController]
        NC[NotesController]
    end

    subgraph "Console Commands"
        PC[ProcessNotes Command]
    end

    %% Service Dependencies
    FIS --> FRS
    PC --> FRS
    PC --> LLM

    %% External Interactions
    FRS --> FS
    LLM --> OPENAI
    FIS --> STORAGE

    %% Controller Dependencies
    FC --> FIS
    NC --> DB

    %% Command Dependencies
    PC --> DB

    %% Data Flow
    FRS -.->|reads files| FS
    FIS -.->|copies files| STORAGE
    FIS -.->|creates records| DB
    LLM -.->|API calls| OPENAI
    LLM -.->|creates categories| DB
    PC -.->|processes notes| DB

    classDef service fill:#e1f5fe
    classDef external fill:#fff3e0
    classDef data fill:#f3e5f5
    classDef controller fill:#e8f5e8
    classDef command fill:#fff8e1

    class FRS,FIS,LLM service
    class FS,OPENAI external
    class DB,STORAGE data
    class FC,NC controller
    class PC command
```

## Service Descriptions

### FileReaderService
- **Purpose**: Handles file system operations and content extraction
- **Capabilities**:
  - Scans directories for supported file formats (txt, md, pdf, rtf)
  - Reads file content with format-specific handling
  - Extracts file metadata (size, timestamps)
  - PDF text extraction using Spatie/PdfToText

### FolderImportService
- **Purpose**: Manages the complete folder import workflow
- **Capabilities**:
  - Validates source directories
  - Copies files to Laravel storage
  - Creates folder and note database records
  - Handles duplicate filename resolution
  - Preserves file timestamps
  - Provides import statistics

### LLMService
- **Purpose**: Integrates with OpenAI for AI-powered note processing
- **Capabilities**:
  - Analyzes content for automatic categorization
  - Generates summaries and key points
  - Creates/finds categories and subcategories
  - Handles API failures with fallback responses
  - JSON response validation

## Service Interactions

1. **Import Flow**: `FolderImportService` → `FileReaderService` → File System
2. **Processing Flow**: `ProcessNotes Command` → `LLMService` → OpenAI API
3. **Content Reading**: `FileReaderService` → File System (various formats)
4. **Category Management**: `LLMService` → Database (categories/subcategories)

## External Dependencies

- **OpenAI API**: For content analysis and summarization
- **Spatie/PdfToText**: For PDF content extraction
- **Laravel Storage**: For file management
- **SQLite Database**: For data persistence

*Generated on: 2025-08-17*