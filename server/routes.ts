import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import express from "express";
import path from "path";

export async function registerRoutes(app: Express): Promise<Server> {
  // put application routes here
  // prefix all routes with /api

  // use storage to perform CRUD operations on the storage interface
  // e.g. storage.insertUser(user) or storage.getUserByUsername(username)

  // Serve standalone assessment from public directory
  const publicPath = path.resolve(import.meta.dirname, "..", "public");
  app.use(express.static(publicPath));
  
  // Redirect /assessment/ to /assessment/index.html
  app.get('/assessment', (req, res) => {
    res.redirect('/assessment/index.html');
  });
  
  app.get('/assessment/', (req, res) => {
    res.redirect('/assessment/index.html');
  });

  const httpServer = createServer(app);

  return httpServer;
}
