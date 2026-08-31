# Hybrid ML REST Endpoints

All endpoints require an authenticated active student session, role middleware, ownership checks, request throttling, and CSRF protection for browser writes. JSON clients should send `Accept: application/json`.

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/student/ml/datasets` | List active built-in datasets and datasets owned by the student |
| POST | `/api/student/ml/datasets/validate` | Validate, sanitize, profile, preview, and score CSV/XLSX without persisting it |
| POST | `/api/student/ml/datasets/upload` | Validate and store a private or class-linked canonical dataset |
| GET | `/api/student/ml/datasets/{system|user}/{id}/quality` | Retrieve the latest quality report for an accessible dataset |
| POST | `/api/student/ml/training-jobs` | Validate a training configuration and enqueue it |
| GET | `/api/student/ml/training-jobs/{id}` | Read progress, stage, result, status URL, or failure details |
| GET | `/api/student/ml/models/{id}` | Read an accessible model, active version, versions, and benchmark comparison |
| DELETE | `/api/student/ml/models/{id}` | Delete an owned user model and all private artifact versions |
| POST | `/api/student/ml/models/{id}/versions/{version}/activate` | Activate an older ready user-model version |
| POST | `/api/student/ml/models/{id}/predictions` | Run a validated prediction and log the result |
| GET | `/api/student/ml/model-versions/{version}/visualizations/{chart}` | Retrieve an authenticated PNG visualization |

Training creation returns HTTP 202 with a status URL. Upload returns HTTP 201. Validation-only analysis returns HTTP 200 and does not write a dataset. Validation failures return HTTP 422. Unauthorized cross-user or cross-class access returns HTTP 403.

## Training request outline

```json
{
  "dataset_type": "system",
  "dataset_id": 1,
  "model_name": "Iris Random Forest Experiment",
  "problem_type": "classification",
  "target_column": "species",
  "features": ["sepal_length", "sepal_width", "petal_length", "petal_width"],
  "algorithm_key": "random_forest",
  "test_size": 0.2,
  "random_state": 42,
  "cross_validation": 5,
  "parameters": {
    "n_estimators": 150,
    "max_depth": 10
  }
}
```

Every parameter is checked against the server catalog. Client-supplied command names, Python source, filesystem paths, estimator class names, and arbitrary keyword arguments are ignored or rejected.

## Visualization security

The `{chart}` segment must match a chart key already stored on the selected model version. The server resolves the path from trusted metadata, confirms a `.png` file under private storage, checks model access, and then streams the image. A request can never supply a filesystem path directly.
