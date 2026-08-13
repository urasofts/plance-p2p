const welcomeScreen = document.getElementById("welcomeScreen");
const appShell = document.getElementById("appShell");
const goExplorerBtn = document.getElementById("goExplorerBtn");

goExplorerBtn?.addEventListener("click", () => {
  welcomeScreen.classList.add("hidden");
  appShell.classList.remove("hidden");
  setTimeout(() => {
    if (typeof cmEditor !== "undefined" && cmEditor) cmEditor.refresh();
  }, 120);
});
