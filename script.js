const welcomeScreen = document.querySelector("#welcomeScreen");
const instructionScreen = document.querySelector("#instructionScreen");
const scanScreen = document.querySelector("#scanScreen");
const diveButton = document.querySelector("#diveButton");
const getStartedButton = document.querySelector('#getStartedButton');
const backToInstructionsButton = document.querySelector('#backToInstructionsButton');
const backButton = document.querySelector("#backButton");
const scanButton = document.querySelector("#scanButton");
const continueButton = document.querySelector("#continueButton");
const restartButton = document.querySelector("#restartButton");
const finishRestartButton = document.querySelector("#finishRestartButton");
const scanStatus = document.querySelector("#scanStatus");
const scanInstructions = document.querySelector("#scanInstructions");
const progressLabel = document.querySelector("#progressLabel");
const progressBar = document.querySelector("#progressBar");
const artifactResult = document.querySelector("#artifactResult");
const detailsResult = document.querySelector("#detailsResult");
const detailsCopy = document.querySelector("#detailsCopy");
const completionResult = document.querySelector("#completionResult");

// These are demo records. Replace names/descriptions with the museum's verified records.
const artifacts = [
  { name: 'Helix Fossil', description: 'Look at the fossil’s spiral shape, texture, and markings. What clues might these features give you about the organism and the environment it lived in?' },
  { name: 'Ammonite Fossil', description: 'Notice the repeating curves and chambers in this sample fossil. Compare its shape with the Helix Fossil and think about how fossils preserve evidence of past life.' },
  { name: 'Trilobite Fossil', description: 'Look for the segmented body and symmetry in this sample artifact. What might its body structure tell you about how it moved or protected itself?' },
  { name: 'Ancient Shell', description: 'Examine the shell’s outline, ridges, and surface pattern. How is it similar to or different from the other objects you discovered?' }
];
let discoveredCount = 0;
let currentArtifact = null;

function showScreen(screenToShow) {
  welcomeScreen.hidden = screenToShow !== welcomeScreen;
  instructionScreen.hidden = screenToShow !== instructionScreen;
  scanScreen.hidden = screenToShow !== scanScreen;
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function updateProgress() {
  progressLabel.textContent = `Artifact ${discoveredCount} of ${artifacts.length} discovered`;
  progressBar.style.width = `${(discoveredCount / artifacts.length) * 100}%`;
}

function resetJourney() {
  discoveredCount = 0;
  currentArtifact = null;
  scanStatus.textContent = 'Camera preview will appear here.';
  scanInstructions.textContent = 'Find the Helix Fossil marker in the exhibit and scan it to begin your investigation.';
  scanButton.textContent = 'Try a sample scan';
  scanButton.disabled = false;
  scanButton.hidden = false;
  artifactResult.hidden = true;
  detailsResult.hidden = true;
  completionResult.hidden = true;
  updateProgress();
}

diveButton.addEventListener("click", () => showScreen(instructionScreen));
getStartedButton.addEventListener('click', () => showScreen(scanScreen));
backToInstructionsButton.addEventListener('click', () => showScreen(instructionScreen));

scanButton.addEventListener("click", () => {
  if (discoveredCount >= artifacts.length) {
    completionResult.hidden = false;
    artifactResult.hidden = true;
    detailsResult.hidden = true;
    scanButton.hidden = true;
    completionResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    return;
  }

  currentArtifact = artifacts[discoveredCount];
  discoveredCount += 1;
  scanStatus.textContent = `${currentArtifact.name} recognized. This is a simulated scan.`;
  scanInstructions.textContent = discoveredCount < artifacts.length
    ? 'Explore this match, then scan the next artifact marker to continue your journey.'
    : 'You found all four artifacts! Explore this match, then finish your journey.';
  artifactResult.querySelector('.eyebrow').textContent = `SAMPLE MATCH ${discoveredCount} OF ${artifacts.length}`;
  artifactResult.querySelector('h2').textContent = currentArtifact.name;
  artifactResult.querySelector('p:not(.eyebrow)').textContent = 'This is a simulated match for the prototype. Later, this area can display the museum’s verified artifact record, image, and collection link.';
  detailsCopy.textContent = currentArtifact.description;
  artifactResult.hidden = false;
  detailsResult.hidden = true;
  completionResult.hidden = true;
  scanButton.textContent = discoveredCount < artifacts.length ? 'Scan next artifact' : 'Finish journey';
  updateProgress();
});

continueButton.addEventListener("click", () => {
  detailsResult.hidden = false;
  detailsResult.scrollIntoView({ behavior: "smooth", block: "nearest" });
});

function restartExperience() {
  resetJourney();
  showScreen(welcomeScreen);
}

restartButton.addEventListener("click", restartExperience);
finishRestartButton.addEventListener('click', restartExperience);
