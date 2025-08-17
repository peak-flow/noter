# Database Entity Relationship Diagram (ERD)

## Note Processor Database Schema

This diagram shows the relationships between all database tables in the Note Processor application.

```mermaid
erDiagram
    folders ||--o{ notes : contains
    notes ||--o| processed_notes : "generates"
    categories ||--o{ subcategories : "has"
    categories ||--o{ processed_notes : "categorizes"
    subcategories ||--o{ processed_notes : "subcategorizes"
    users ||--o{ folders : "owns (future)"

    folders {
        bigint id PK
        string name
        string source_path
        string storage_path
        integer total_files
        integer processed_files
        timestamp imported_at
        timestamp created_at
        timestamp updated_at
    }

    notes {
        bigint id PK
        bigint folder_id FK
        string file_path
        string file_name
        string format
        longtext original_content
        integer file_size
        timestamp file_created_at
        timestamp file_modified_at
        boolean is_processed
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        string name
        text description
        integer note_count
        timestamp created_at
        timestamp updated_at
    }

    subcategories {
        bigint id PK
        bigint category_id FK
        string name
        text description
        integer note_count
        timestamp created_at
        timestamp updated_at
    }

    processed_notes {
        bigint id PK
        bigint note_id FK
        bigint category_id FK
        bigint subcategory_id FK
        string title
        longtext summary
        longtext key_points
        json metadata
        timestamp created_at
        timestamp updated_at
    }

    users {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
    }
```

## Key Relationships

1. **Folders → Notes**: One folder can contain many notes (1:N)
2. **Notes → Processed Notes**: Each note can have one processed version (1:1)
3. **Categories → Subcategories**: One category can have many subcategories (1:N)
4. **Categories → Processed Notes**: One category can contain many processed notes (1:N)
5. **Subcategories → Processed Notes**: One subcategory can contain many processed notes (1:N)

## Database Indexes

- `notes.file_path` - For quick file lookup
- `notes.is_processed` - For filtering processed/unprocessed notes
- `folders.imported_at` - For chronological folder listing
- `processed_notes.category_id, subcategory_id` - For category-based queries

## Foreign Key Constraints

- `notes.folder_id` → `folders.id` (CASCADE DELETE)
- `processed_notes.note_id` → `notes.id` (CASCADE DELETE)
- `processed_notes.category_id` → `categories.id` (CASCADE DELETE)
- `processed_notes.subcategory_id` → `subcategories.id` (SET NULL)
- `subcategories.category_id` → `categories.id` (CASCADE DELETE)

*Generated on: 2025-08-17*