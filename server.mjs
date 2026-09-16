import http from "node:http";
import fs from "node:fs";
import path from "node:path";
const root = path.resolve("dist");
const types = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "text/javascript; charset=utf-8",
  ".json": "application/json",
  ".txt": "text/plain; charset=utf-8",
  ".xml": "application/xml",
  ".webp": "image/webp",
  ".jpg": "image/jpeg",
  ".png": "image/png",
  ".mp4": "video/mp4",
  ".svg": "image/svg+xml",
  ".woff2": "font/woff2",
  ".ttf": "font/ttf",
};
http
  .createServer((req, res) => {
    try {
      if (!["GET", "HEAD"].includes(req.method)) {
        res.writeHead(405, { Allow: "GET, HEAD" });
        return res.end();
      }
      const requested = decodeURIComponent(
        new URL(req.url, "http://localhost").pathname,
      );
      let file = path.resolve(root, "." + requested);
      if (file !== root && !file.startsWith(root + path.sep)) {
        res.writeHead(403);
        return res.end();
      }
      if (fs.existsSync(file) && fs.statSync(file).isDirectory())
        file = path.join(file, "index.html");
      if (!fs.existsSync(file) || !fs.statSync(file).isFile()) {
        res.writeHead(404);
        return res.end("Not found");
      }
      const size = fs.statSync(file).size;
      res.setHeader(
        "Content-Type",
        types[path.extname(file)] || "application/octet-stream",
      );
      res.setHeader("X-Content-Type-Options", "nosniff");
      res.setHeader("Accept-Ranges", "bytes");
      let start = 0,
        end = size - 1;
      const range = req.headers.range;
      if (range) {
        const m = /^bytes=(\d+)-(\d*)$/.exec(range);
        if (!m) {
          res.writeHead(416, { "Content-Range": `bytes */${size}` });
          return res.end();
        }
        start = Number(m[1]);
        end = m[2] ? Math.min(Number(m[2]), size - 1) : size - 1;
        if (start > end || start >= size) {
          res.writeHead(416, { "Content-Range": `bytes */${size}` });
          return res.end();
        }
        res.statusCode = 206;
        res.setHeader("Content-Range", `bytes ${start}-${end}/${size}`);
      }
      res.setHeader("Content-Length", end - start + 1);
      if (req.method === "HEAD") return res.end();
      fs.createReadStream(file, { start, end })
        .on("error", () => res.destroy())
        .pipe(res);
    } catch {
      res.writeHead(400);
      res.end("Bad request");
    }
  })
  .listen(4173, "127.0.0.1");
