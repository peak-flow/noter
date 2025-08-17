# MVC Structure Diagram

## Laravel MVC Architecture for Note Processor

This diagram shows the Model-View-Controller structure and relationships in the Note Processor application.

```mermaid
graph TB
    subgraph "Views (Blade Templates)"
        VL[layouts/app.blade.php]
        
        subgraph "Folder Views"
            VF1[folders/index.blade.php]
            VF2[folders/create.blade.php]
            VF3[folders/show.blade.php]
        end
        
        subgraph "Note Views"
            VN1[notes/index.blade.php]
            VN2[notes/category.blade.php]
            VN3[notes/show.blade.php]
            VN4[notes/search.blade.php]
        end
    end

    subgraph "Controllers"
        FC[FoldersController]
        NC[NotesController]
        
        subgraph "Controller Methods"
            subgraph "FoldersController Methods"
                FC1[index - List folders]
                FC2[create - Show import form]
                FC3[store - Import folder]
                FC4[show - Folder details]
                FC5[destroy - Delete folder]
            end
            
            subgraph "NotesController Methods"
                NC1[index - Dashboard]
                NC2[category - Category view]
                NC3[show - Note details]
                NC4[search - Search notes]
            end
        end
    end

    subgraph "Models (Eloquent)"
        M1[Folder]
        M2[Note]
        M3[Category]
        M4[Subcategory]
        M5[ProcessedNote]
        M6[User]
        
        subgraph "Model Relationships"
            REL1[Folder hasMany Notes]
            REL2[Note hasOne ProcessedNote]
            REL3[Category hasMany Subcategories]
            REL4[Category hasMany ProcessedNotes]
            REL5[Subcategory hasMany ProcessedNotes]
            REL6[Note belongsTo Folder]
            REL7[ProcessedNote belongsTo Note]
            REL8[ProcessedNote belongsTo Category]
            REL9[ProcessedNote belongsTo Subcategory]
        end
    end

    subgraph "Routes (web.php)"
        R1[GET / → NotesController@index]
        R2[GET /category/{id} → NotesController@category]
        R3[GET /note/{id} → NotesController@show]
        R4[GET /search → NotesController@search]
        R5[GET /folders → FoldersController@index]
        R6[GET /folders/create → FoldersController@create]
        R7[POST /folders → FoldersController@store]
        R8[GET /folders/{folder} → FoldersController@show]
        R9[DELETE /folders/{folder} → FoldersController@destroy]
    end

    subgraph "Services (Business Logic)"
        S1[FileReaderService]
        S2[FolderImportService]
        S3[LLMService]
    end

    subgraph "Database"
        DB[(SQLite Database)]
        T1[folders table]
        T2[notes table]
        T3[categories table]
        T4[subcategories table]
        T5[processed_notes table]
        T6[users table]
    end

    %% Route to Controller connections
    R1 --> NC1
    R2 --> NC2
    R3 --> NC3
    R4 --> NC4
    R5 --> FC1
    R6 --> FC2
    R7 --> FC3
    R8 --> FC4
    R9 --> FC5

    %% Controller to View connections
    FC1 --> VF1
    FC2 --> VF2
    FC3 --> VF1
    FC4 --> VF3
    FC5 --> VF1

    NC1 --> VN1
    NC2 --> VN2
    NC3 --> VN3
    NC4 --> VN4

    %% Controller to Model connections
    FC --> M1
    FC --> M2
    NC --> M3
    NC --> M5
    NC --> M2

    %% Controller to Service connections
    FC --> S2
    NC --> DB

    %% Service dependencies
    S2 --> S1
    S3 --> M3
    S3 --> M4

    %% Model to Database connections
    M1 --> T1
    M2 --> T2
    M3 --> T3
    M4 --> T4
    M5 --> T5
    M6 --> T6

    %% View Layout connections
    VF1 --> VL
    VF2 --> VL
    VF3 --> VL
    VN1 --> VL
    VN2 --> VL
    VN3 --> VL
    VN4 --> VL

    %% Styling
    classDef view fill:#e3f2fd,stroke:#1976d2
    classDef controller fill:#f3e5f5,stroke:#7b1fa2
    classDef model fill:#e8f5e8,stroke:#388e3c
    classDef route fill:#fff3e0,stroke:#f57c00
    classDef service fill:#fce4ec,stroke:#c2185b
    classDef database fill:#f1f8e9,stroke:#689f38

    class VL,VF1,VF2,VF3,VN1,VN2,VN3,VN4 view
    class FC,NC,FC1,FC2,FC3,FC4,FC5,NC1,NC2,NC3,NC4 controller
    class M1,M2,M3,M4,M5,M6,REL1,REL2,REL3,REL4,REL5,REL6,REL7,REL8,REL9 model
    class R1,R2,R3,R4,R5,R6,R7,R8,R9 route
    class S1,S2,S3 service
    class DB,T1,T2,T3,T4,T5,T6 database
```

## MVC Component Details

### Models (Eloquent ORM)
- **Folder**: Manages imported folder metadata and relationships
- **Note**: Represents original files with content and metadata  
- **Category**: Top-level categorization created by AI analysis
- **Subcategory**: Secondary categorization within categories
- **ProcessedNote**: AI-processed version with summary and categorization
- **User**: Authentication model (prepared for future use)

### Views (Blade Templates)
- **Layout**: `app.blade.php` - Main application layout with navigation
- **Folder Views**: Import form, folder listing, and individual folder details
- **Note Views**: Dashboard, category browsing, note details, and search results

### Controllers
- **FoldersController**: Handles folder import, listing, and management
- **NotesController**: Manages note display, categorization, and search functionality

### Routes
- **Dashboard Route**: `/` - Main entry point showing categories and statistics
- **Folder Routes**: CRUD operations for folder management
- **Note Routes**: Category browsing, individual note viewing, and search
- **RESTful Design**: Following Laravel conventions with named routes

## Data Flow Patterns

### Request → Response Flow
1. **Route** receives HTTP request
2. **Controller** processes request and business logic
3. **Model** handles data operations with database
4. **Service** provides additional business logic (file operations, AI processing)
5. **View** renders response using data from controller
6. **Layout** provides consistent UI structure

### Dependency Injection
- Controllers receive Services through constructor injection
- Services receive other Services through constructor injection
- Models use Eloquent relationships for data access

### Separation of Concerns
- **Models**: Data structure and relationships
- **Controllers**: HTTP request handling and response formatting
- **Services**: Business logic and external API integration
- **Views**: Presentation and user interface

*Generated on: 2025-08-17*