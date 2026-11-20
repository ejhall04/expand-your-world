const welcomeScreen = document.querySelector("#welcomeScreen");
const instructionScreen = document.querySelector("#instructionScreen");
const scanScreen = document.querySelector("#scanScreen");
const diveButton = document.querySelector("#diveButton");
const getStartedButton = document.querySelector("#getStartedButton");
const backToInstructionsButton = document.querySelector("#backToInstructionsButton");
const scanButton = document.querySelector("#scanButton");
const artifactModal = document.querySelector("#artifactModal");
const exploreArtifactButton = document.querySelector("#exploreArtifactButton");
const scanNextArtifactButton = document.querySelector("#scanNextArtifactButton");
const modalArtifactEyebrow = document.querySelector("#modalArtifactEyebrow");
const modalArtifactTitle = document.querySelector("#modalArtifactTitle");
const modalArtifactCopy = document.querySelector("#modalArtifactCopy");
const finishRestartButton = document.querySelector("#finishRestartButton");
const scanInstructions = document.querySelector("#scanInstructions");
const progressLabel = document.querySelector("#progressLabel");
const progressBar = document.querySelector("#progressBar");
const scanFrame = document.querySelector("#scan-frame");
const scanStatus = document.querySelector("#scanStatus");
const scanPreview = document.querySelector(".scan-preview");
const scanStatusIcon = scanStatus.querySelector(".status-icon");
const scanStatusText = scanStatus.querySelector("p");
const completionResult = document.querySelector("#completionResult");

// Demo QR values are 1, 2, 3, and 4. Replace these with verified museum records later.
const artifacts = [
  { name: "Cincinnati city fossil", description: "This animal was related to starfish and lived in Cincinnati’s ancient sea roughly 450 million years ago. What would you look for to spot an ancient relative of a starfish?" },
  { name: "Outgrowing a Skeleton", description: "Why might a slab contain so many apparently “headless” trilobite bodies? They weren’t headless bodies after all. Trilobites grew by molting—crawling out of an old outer skeleton. While soft, they were especially vulnerable." },
  { name: "Ordovician donuts", description: "What do you think caused the hole in this fossil? The mystery is real. This colony may have grown around another object—but the object left no trace. Scientists still do not know exactly what happened." },
  { name: "Silurian–Devonian case", description: "How do you think life on Earth would recover if there was a change in temperature? After a cold spell, the Earth warmed, seas rose, and ecosytems took millions of years to recover, but returned with greater diversity than before." }
];

let discoveredCount = 0;
let currentArtifact = null;
let scanner = null;
let scannerRunning = false;
let scanLocked = false;
let modalStage = "match";

async function showScreen(screenToShow) {
  welcomeScreen.hidden = screenToShow !== welcomeScreen;
  instructionScreen.hidden = screenToShow !== instructionScreen;
  scanScreen.hidden = screenToShow !== scanScreen;
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function updateProgress() {
  progressLabel.textContent = `Artifact ${discoveredCount} of ${artifacts.length} discovered`;
  progressBar.style.width = `${(discoveredCount / artifacts.length) * 100}%`;
}

function setScanStatus(state, message) {
  scanFrame.classList.toggle("detected", state === "detected");
  scanFrame.classList.toggle("error", state === "error");
  scanStatus.classList.toggle("is-detected", state === "detected");
  scanStatus.classList.toggle("is-error", state === "error");

  if (state === "detected") {
    scanStatusIcon.textContent = "✓";
  } else if (state === "error") {
    scanStatusIcon.textContent = "!";
  } else {
    scanStatusIcon.textContent = "⌖";
  }
  scanStatusText.textContent = message;
}

function resetScanVisuals() {
  setScanStatus("ready", "Camera is ready. Point it at the next artifact's QR code.");
  scanPreview.classList.remove("camera-active");
  scanLocked = false;
}

async function stopScanner() {
  if (scanner && scannerRunning) {
    try {
      await scanner.stop();
    } catch (error) {
      console.warn("Could not stop the QR scanner cleanly:", error);
    } finally {
      scannerRunning = false;
    }
  }
}

async function startScanner() {
  if (scannerRunning || discoveredCount >= artifacts.length) return;

  try {
    if (!scanner) scanner = new Html5Qrcode("qr-reader");

    resetScanVisuals();
    scanStatusText.textContent = "Looking for a QR code…";
    scanPreview.classList.add("camera-active");
    scanButton.disabled = true;
    scanButton.textContent = "Scanning…";

    await scanner.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      handleQrDetected,
      () => {
        // No QR code in this video frame; keep scanning without changing the UI.
      }
    );

    scannerRunning = true;
    // Keep the action button disabled while the live camera is scanning.
    scanButton.disabled = true;
  } catch (error) {
    console.error("Error starting the scanner:", error);
    scanPreview.classList.remove("camera-active");
    scanButton.disabled = false;
    scanButton.textContent = "Try scanning again";
    setScanStatus("error", "Camera could not start. Check camera permission and try again.");
  }
}

function openArtifactModal() {
  modalStage = "match";
  modalArtifactEyebrow.textContent = `SAMPLE MATCH ${discoveredCount} OF ${artifacts.length}`;
  modalArtifactTitle.textContent = currentArtifact.name;
  modalArtifactCopy.textContent = "You expanded your world! Click Explore this artifact to learn more about it, or Scan next artifact to continue on your journey.";
  exploreArtifactButton.textContent = "Explore this artifact";
  scanNextArtifactButton.hidden = false;
  scanNextArtifactButton.textContent = discoveredCount >= artifacts.length
    ? "Finish your journey"
    : "Scan next artifact";
  artifactModal.hidden = false;
  document.body.classList.add("modal-open");
  exploreArtifactButton.focus();
}

function closeArtifactModal() {
  artifactModal.hidden = true;
  document.body.classList.remove("modal-open");
  if (discoveredCount >= artifacts.length && !completionResult.hidden) {
    completionResult.scrollIntoView({ behavior: "smooth", block: "nearest" });
  } else if (discoveredCount >= artifacts.length) {
    completionResult.hidden = false;
    completionResult.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }
  if (discoveredCount >= artifacts.length) {
    finishRestartButton.focus();
  }
}

function showArtifactInvestigation() {
  modalStage = "investigation";
  modalArtifactEyebrow.textContent = "YOUR INVESTIGATION";
  modalArtifactTitle.textContent = "Take a closer look";
  modalArtifactCopy.textContent = currentArtifact.description;
  exploreArtifactButton.textContent = discoveredCount >= artifacts.length
    ? "Finish your journey"
    : "Back to scanning";
  scanNextArtifactButton.hidden = true;
}

async function handleQrDetected(decodedText) {
  // html5-qrcode can report the same code on several consecutive frames.
  if (scanLocked) return;

  const expectedCode = String(discoveredCount + 1);
  if (String(decodedText).trim() !== expectedCode) {
    setScanStatus("error", "That isn't the next artifact. Find the marker for the next stop and try again.");
    return;
  }

  scanLocked = true;
  currentArtifact = artifacts[discoveredCount];
  discoveredCount += 1;

  setScanStatus("detected", `${currentArtifact.name} detected!`);
  scanInstructions.textContent = discoveredCount < artifacts.length
    ? "Artifact found! Explore this match, then scan the next artifact marker to continue your journey."
    : "You found all four artifacts! Explore this match, then finish your journey.";

  completionResult.hidden = true;
  updateProgress();

  // Keep the existing camera stream alive between artifacts so the browser
  // does not need to request camera access again for every scan.
  // Stop it only after the fourth artifact has been found.
  if (discoveredCount >= artifacts.length) {
    await stopScanner();
    scanButton.hidden = true;
  } else {
    // The scanner remains active, but scanLocked prevents duplicate matches
    // until the visitor chooses one of the two actions in the pop-up.
    scanButton.textContent = "Scanning paused";
    scanButton.disabled = true;
  }
  openArtifactModal();
}

function resetJourney() {
  discoveredCount = 0;
  currentArtifact = null;
  scanInstructions.textContent = "Find the Helix Fossil marker in the exhibit and scan it to begin your investigation.";
  scanButton.textContent = "Try a sample scan";
  scanButton.disabled = false;
  scanButton.hidden = false;
  closeArtifactModal();
  completionResult.hidden = true;
  resetScanVisuals();
  updateProgress();
}

diveButton.addEventListener("click", () => showScreen(instructionScreen));
getStartedButton.addEventListener("click", () => showScreen(scanScreen));
backToInstructionsButton.addEventListener("click", async () => {
  await stopScanner();
  scanButton.disabled = false;
  scanButton.textContent = discoveredCount > 0 ? "Scan Next Artifact" : "Try a sample scan";
  showScreen(instructionScreen);
});

async function resumeScanningAfterModal() {
  closeArtifactModal();
  if (discoveredCount >= artifacts.length) {
    completionResult.hidden = false;
    completionResult.scrollIntoView({ behavior: "smooth", block: "nearest" });
    finishRestartButton.focus();
    return;
  }

  // Reset the green detection frame and unlock QR callbacks. The current
  // html5-qrcode stream is reused, so no new permission prompt is needed.
  resetScanVisuals();
  if (scannerRunning) {
    scanStatusText.textContent = "Looking for the next QR code…";
    scanPreview.classList.add("camera-active");
    scanButton.textContent = "Scanning…";
    scanButton.disabled = true;
  } else {
    // Recovery path in case the browser or device stopped the stream.
    await startScanner();
  }
}

scanButton.addEventListener("click", async () => {
  // This button is used to start the first scan or recover a stopped stream.
  await startScanner();
});

exploreArtifactButton.addEventListener("click", async () => {
  if (modalStage === "match") {
    showArtifactInvestigation();
    return;
  }

  // In the investigation view, Back to scanning immediately resumes the
  // camera; visitors do not need to press the button beneath the preview.
  await resumeScanningAfterModal();
});

scanNextArtifactButton.addEventListener("click", resumeScanningAfterModal);

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && !artifactModal.hidden) {
    // Treat Escape as choosing the non-investigation path, avoiding a closed
    // pop-up that leaves the scanner locked.
    resumeScanningAfterModal();
  }
});

async function restartExperience() {
  await stopScanner();
  resetJourney();
  showScreen(welcomeScreen);
}

finishRestartButton.addEventListener("click", restartExperience);

// Stop the camera when the visitor leaves the page.
window.addEventListener("pagehide", () => {
  if (scanner && scannerRunning) scanner.stop().catch(() => {});
});
