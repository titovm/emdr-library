from __future__ import annotations

import html
import re
import subprocess
import sys
import zipfile
from pathlib import Path

from pypdf import PdfReader


for path in [Path(p) for p in sys.argv[1:]]:
    text = ""
    try:
        if path.suffix.lower() == ".pptx":
            with zipfile.ZipFile(path) as zf:
                for name in ["ppt/slides/slide1.xml", "ppt/slides/slide2.xml"]:
                    if name in zf.namelist():
                        raw = zf.read(name).decode("utf-8", "ignore")
                        text += " ".join(html.unescape(x) for x in re.findall(r"<a:t>(.*?)</a:t>", raw, re.S)) + " | "
        elif path.suffix.lower() == ".pdf":
            reader = PdfReader(str(path))
            text = reader.pages[0].extract_text() or ""
        else:
            proc = subprocess.run(["strings", "-el", str(path)], capture_output=True, text=True, timeout=15)
            text = proc.stdout
    except Exception as exc:
        text = f"ОШИБКА: {exc}"
    text = re.sub(r"\s+", " ", text).strip()[:1200]
    print(f"\n### {path.name}\n{text}")
