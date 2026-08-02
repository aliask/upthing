# Shared setup for the auto/ scripts. Sourced, not executed.
#
# Callers are expected to have already cd'd to the repo root.

# Production runs Docker, local machines here run podman. Prefer whichever
# is present rather than making every script care.
if command -v docker >/dev/null 2>&1; then
  CONTAINER=docker
  COMPOSE=(docker compose)
elif command -v podman >/dev/null 2>&1; then
  CONTAINER=podman
  COMPOSE=(podman compose)
else
  echo "auto: neither docker nor podman is installed." >&2
  exit 1
fi

# The dev stack is the production file plus the local overrides.
DEV_COMPOSE=(-f docker-compose.yml -f docker-compose.dev.yml)

# Image tag used for locally built test images. CI overrides this and sets
# SKIP_IMAGE_BUILD, so it can reuse the buildx-cached image it already built
# rather than building a second, uncached copy.
TEST_TAG="${TEST_TAG:-upthing-test:local}"

build_test_image() {
  if [ -n "${SKIP_IMAGE_BUILD:-}" ]; then
    echo "==> Using prebuilt image $TEST_TAG"
    return
  fi

  echo "==> Building test image"
  "$CONTAINER" build -f docker/Dockerfile --target test -t "$TEST_TAG" .
}
