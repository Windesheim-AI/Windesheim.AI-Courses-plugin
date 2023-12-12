# Courses WordPress Plugin

## Description

Courses is a WordPress plugin that provides a REST API for managing AI-courses. It allows users to follow a courses to learn more about AI.

## Installation

1. Download the plugin files.
2. Upload the plugin files to the `/wp-content/plugins/Courses` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage

The plugin registers several endpoints under the `winai/v1` namespace:

- `GET /courses`: Retrieves all AI courses.
- `GET /courses/{id}`: Retrieves a specific AI course by its ID.
- `POST /courses`: Creates a new AI course.
- `PUT /courses/{id}`: Updates a specific AI course by its ID.
- `DELETE /courses/{id}`: Deletes a specific AI course by its ID.

All endpoints require the user to be logged in. The `POST /tools` and `POST /services` endpoints additionally require the user to have the `edit_posts` capability.

## Versioning

The plugin version is added to the headers of every API response as `X-WingAI-Version`.

## License

This project is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).